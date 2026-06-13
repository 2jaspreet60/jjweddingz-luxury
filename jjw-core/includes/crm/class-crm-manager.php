<?php
/**
 * class-crm-manager.php — JJ WeddingZ CRM & Task Management Engine
 *
 * Handles:
 * - Lead Dashboard summary metrics & pipeline visualizations
 * - Inline stage transitions, team assignments, notes & event metadata
 * - CRM Task creation, priorities, due dates, and assignments
 * - AJAX handlers for lead & task actions
 * - Lead CSV exporter
 *
 * @package JJW_Core
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_CRM_Manager {

    public function __construct() {
        // Lead AJAX Actions
        add_action( 'wp_ajax_jjwz_add_manual_lead', [ $this, 'handle_add_manual_lead' ] );
        add_action( 'wp_ajax_jjwz_update_lead',        [ $this, 'handle_update_lead' ] );
        add_action( 'wp_ajax_jjwz_delete_lead',        [ $this, 'handle_delete_lead' ] );

        // Task AJAX Actions
        add_action( 'wp_ajax_jjwz_get_lead_tasks',     [ $this, 'handle_get_lead_tasks' ] );
        add_action( 'wp_ajax_jjwz_add_lead_task',      [ $this, 'handle_add_lead_task' ] );
        add_action( 'wp_ajax_jjwz_toggle_lead_task',   [ $this, 'handle_toggle_lead_task' ] );
        add_action( 'wp_ajax_jjwz_delete_lead_task',   [ $this, 'handle_delete_lead_task' ] );
    }

    /**
     * Renders the Premium CRM Dashboard interface inside WordPress Admin
     */
    public static function render_dashboard(): void {
        global $wpdb;
        $leads_table = $wpdb->prefix . 'jjwz_leads';
        $tasks_table = $wpdb->prefix . 'jjwz_tasks';

        // Retrieve all leads for dashboard metric calculation
        $all_leads = [];
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$leads_table'" ) === $leads_table ) {
            $all_leads = $wpdb->get_results( "SELECT * FROM {$leads_table} ORDER BY created_at DESC", ARRAY_A ) ?: [];
        }

        // Calculations
        $total_leads = count( $all_leads );
        $new_leads   = 0;
        $due_follows = 0;
        $booked_clients = 0;
        $revenue_pipeline = 0;
        $today = date( 'Y-m-d' );

        foreach ( $all_leads as $lead ) {
            $status = $lead['status'] ?? 'New Lead';
            if ( $status === 'New Lead' ) {
                $new_leads++;
            }
            if ( $status === 'Booked' ) {
                $booked_clients++;
            }
            if ( $status === 'Follow Up' && ! empty( $lead['follow_up_date'] ) && $lead['follow_up_date'] <= $today ) {
                $due_follows++;
            }
            if ( $status !== 'Lost' ) {
                $clean_budget = (float) preg_replace( '/[^\d.]/', '', $lead['budget'] ?? '0' );
                $revenue_pipeline += $clean_budget;
            }
        }

        // Available Users / Team members for assignments
        $team_users = get_users( [ 'role__in' => [ 'administrator', 'editor', 'author' ] ] );

        // Available CPT Services
        $services_posts = get_posts( [ 'post_type' => 'jjwz_service', 'posts_per_page' => -1, 'post_status' => 'publish' ] );

        // Filter parameters
        $filter_stage   = sanitize_text_field( $_GET['crm_stage'] ?? '' );
        $filter_brand   = sanitize_text_field( $_GET['crm_brand'] ?? '' );
        $filter_service = sanitize_text_field( $_GET['crm_service'] ?? '' );
        $filter_search  = sanitize_text_field( $_GET['crm_search'] ?? '' );

        // Build filtered leads query
        $where_clauses = [ '1=1' ];
        if ( ! empty( $filter_stage ) ) {
            $where_clauses[] = $wpdb->prepare( "status = %s", $filter_stage );
        }
        if ( ! empty( $filter_brand ) ) {
            $where_clauses[] = $wpdb->prepare( "brand = %s", $filter_brand );
        }
        if ( ! empty( $filter_service ) ) {
            $where_clauses[] = $wpdb->prepare( "service = %s", $filter_service );
        }
        if ( ! empty( $filter_search ) ) {
            $search_wild = '%' . $wpdb->esc_like( $filter_search ) . '%';
            $where_clauses[] = $wpdb->prepare( "(name LIKE %s OR email LIKE %s OR phone LIKE %s OR city LIKE %s OR venue LIKE %s)", $search_wild, $search_wild, $search_wild, $search_wild, $search_wild );
        }

        $where_sql = implode( ' AND ', $where_clauses );
        $filtered_leads = [];
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$leads_table'" ) === $leads_table ) {
            $filtered_leads = $wpdb->get_results( "SELECT * FROM {$leads_table} WHERE {$where_sql} ORDER BY created_at DESC", ARRAY_A ) ?: [];
        }

        $stages = [
            'New Lead', 'Contacted', 'Follow Up', 'Meeting Scheduled',
            'Quote Sent', 'Negotiation', 'Booked', 'Completed', 'Lost'
        ];
        ?>
        <div class="jjwz-crm-container" style="font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Helvetica Neue,sans-serif;color:#2d3748;padding-right:15px;">
            <!-- Stats Header -->
            <div class="jjwz-crm-stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:15px;margin-bottom:25px;">
                <div class="jjwz-stat-card" style="background:#fff;border:1px solid #e2e8f0;border-left:4px solid #c9a96e;border-radius:6px;padding:15px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                    <div style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#718096;font-weight:600;">Total Leads</div>
                    <div style="font-size:24px;font-weight:700;color:#1a202c;margin-top:5px;"><?php echo $total_leads; ?></div>
                </div>
                <div class="jjwz-stat-card" style="background:#fff;border:1px solid #e2e8f0;border-left:4px solid #3182ce;border-radius:6px;padding:15px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                    <div style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#718096;font-weight:600;">New Inquiries</div>
                    <div style="font-size:24px;font-weight:700;color:#2b6cb0;margin-top:5px;"><?php echo $new_leads; ?></div>
                </div>
                <div class="jjwz-stat-card" style="background:#fff;border:1px solid #e2e8f0;border-left:4px solid #e53e3e;border-radius:6px;padding:15px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                    <div style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#718096;font-weight:600;">Follow Ups Due</div>
                    <div style="font-size:24px;font-weight:700;color:#c53030;margin-top:5px;"><?php echo $due_follows; ?></div>
                </div>
                <div class="jjwz-stat-card" style="background:#fff;border:1px solid #e2e8f0;border-left:4px solid #38a169;border-radius:6px;padding:15px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                    <div style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#718096;font-weight:600;">Booked Clients</div>
                    <div style="font-size:24px;font-weight:700;color:#2f855a;margin-top:5px;"><?php echo $booked_clients; ?></div>
                </div>
                <div class="jjwz-stat-card" style="background:#fff;border:1px solid #e2e8f0;border-left:4px solid #805ad5;border-radius:6px;padding:15px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                    <div style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#718096;font-weight:600;">Revenue Pipeline</div>
                    <div style="font-size:24px;font-weight:700;color:#6b46c1;margin-top:5px;">₹<?php echo number_format( $revenue_pipeline ); ?></div>
                </div>
            </div>

            <!-- Toolbar / Filters -->
            <div class="jjwz-crm-toolbar" style="background:#fff;border:1px solid #e2e8f0;border-radius:6px;padding:15px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:15px;">
                <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin:0;">
                    <input type="hidden" name="page" value="jjwz-core-settings-crm">
                    
                    <select name="crm_stage" style="min-width:140px;height:30px;">
                        <option value="">All Stages</option>
                        <?php foreach ( $stages as $stg ) : ?>
                            <option value="<?php echo esc_attr( $stg ); ?>" <?php selected( $filter_stage, $stg ); ?>><?php echo esc_html( $stg ); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="crm_brand" style="min-width:140px;height:30px;">
                        <option value="">All Brands</option>
                        <option value="JJ WeddingZ" <?php selected( $filter_brand, 'JJ WeddingZ' ); ?>>JJ WeddingZ</option>
                        <option value="The Baby StudioZ" <?php selected( $filter_brand, 'The Baby StudioZ' ); ?>>The Baby StudioZ</option>
                    </select>

                    <select name="crm_service" style="min-width:140px;height:30px;">
                        <option value="">All Services</option>
                        <?php foreach ( $services_posts as $svc ) : ?>
                            <option value="<?php echo esc_attr( $svc->post_title ); ?>" <?php selected( $filter_service, $svc->post_title ); ?>><?php echo esc_html( $svc->post_title ); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <input type="text" name="crm_search" value="<?php echo esc_attr( $filter_search ); ?>" placeholder="Search name, phone, city..." style="width:200px;height:30px;line-height:30px;">

                    <button type="submit" class="button button-secondary">🔍 Filter</button>
                    <?php if ( ! empty( $filter_stage ) || ! empty( $filter_brand ) || ! empty( $filter_service ) || ! empty( $filter_search ) ) : ?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=jjwz-core-settings-crm' ) ); ?>" class="button button-link" style="color:#e53e3e;">Clear</a>
                    <?php endif; ?>
                </form>

                <div style="display:flex;gap:10px;">
                    <button type="button" class="button button-primary jjwz-open-add-lead" style="background:#c9a96e;border-color:#c9a96e;font-weight:600;">➕ Add Manual Lead</button>
                    <a href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=jjwz_export_leads&nonce=' . wp_create_nonce( 'jjwz_export_leads' ) ) ); ?>" class="button button-secondary">📥 Export CSV</a>
                </div>
            </div>

            <!-- Leads Pipeline Table -->
            <div class="jjwz-crm-table-container" style="background:#fff;border:1px solid #e2e8f0;border-radius:6px;box-shadow:0 1px 3px rgba(0,0,0,0.05);overflow-x:auto;">
                <table class="wp-list-table widefat fixed striped" style="border:none;">
                    <thead>
                        <tr style="background:#faf8f5;">
                            <th style="font-weight:600;padding:12px 10px;">Lead Info</th>
                            <th style="font-weight:600;padding:12px 10px;">Event Details</th>
                            <th style="font-weight:600;padding:12px 10px;">Brand & Source</th>
                            <th style="font-weight:600;padding:12px 10px;">Lead Stage</th>
                            <th style="font-weight:600;padding:12px 10px;">Follow-up / Assigned</th>
                            <th style="font-weight:600;padding:12px 10px;text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( ! empty( $filtered_leads ) ) : ?>
                            <?php foreach ( $filtered_leads as $lead ) : 
                                $lead_id = $lead['id'];
                                // Get task count
                                $task_count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$tasks_table} WHERE lead_id = %d AND status = 'Pending'", $lead_id ) );
                            ?>
                            <tr id="jjwz-lead-row-<?php echo $lead_id; ?>">
                                <td style="padding:12px 10px;vertical-align:top;">
                                    <strong><?php echo esc_html( $lead['name'] ); ?></strong><br>
                                    <span style="font-size:12px;color:#718096;">
                                        📞 <?php echo esc_html( $lead['phone'] ?: '—' ); ?><br>
                                        ✉️ <?php echo esc_html( $lead['email'] ); ?><br>
                                        📍 <?php echo esc_html( $lead['city'] ?: '—' ); ?>
                                    </span>
                                </td>
                                <td style="padding:12px 10px;vertical-align:top;">
                                    <span style="font-weight:500;color:#c9a96e;"><?php echo esc_html( $lead['service'] ?: 'Photography' ); ?></span><br>
                                    <span style="font-size:12px;color:#718096;">
                                        Type: <?php echo esc_html( $lead['event_type'] ?: '—' ); ?><br>
                                        Date: <?php echo esc_html( $lead['event_date'] ?: '—' ); ?><br>
                                        Budget: ₹<?php echo esc_html( $lead['budget'] ?: '0' ); ?><br>
                                        Venue: <?php echo esc_html( $lead['venue'] ?: '—' ); ?>
                                    </span>
                                </td>
                                <td style="padding:12px 10px;vertical-align:top;">
                                    <span class="badge" style="background:#edf2f7;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:600;color:#4a5568;">
                                        <?php echo esc_html( $lead['brand'] ); ?>
                                    </span><br>
                                    <span style="font-size:12px;color:#a0aec0;display:block;margin-top:5px;">
                                        Source: <?php echo esc_html( $lead['source'] ?: 'Website' ); ?>
                                    </span>
                                </td>
                                <td style="padding:12px 10px;vertical-align:top;">
                                    <select class="jjwz-crm-stage-select" data-lead-id="<?php echo $lead_id; ?>" style="width:100%;height:28px;font-size:12px;">
                                        <?php foreach ( $stages as $stg ) : ?>
                                            <option value="<?php echo esc_attr( $stg ); ?>" <?php selected( $lead['status'], $stg ); ?>><?php echo esc_html( $stg ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td style="padding:12px 10px;vertical-align:top;">
                                    <input type="date" class="jjwz-crm-followup-input" data-lead-id="<?php echo $lead_id; ?>" value="<?php echo esc_attr( $lead['follow_up_date'] ?? '' ); ?>" style="width:100%;font-size:12px;height:28px;padding:0 5px;box-sizing:border-box;margin-bottom:5px;"><br>
                                    <select class="jjwz-crm-assignee-select" data-lead-id="<?php echo $lead_id; ?>" style="width:100%;height:28px;font-size:12px;">
                                        <option value="0">Unassigned</option>
                                        <?php foreach ( $team_users as $usr ) : ?>
                                            <option value="<?php echo $usr->ID; ?>" <?php selected( $lead['assigned_user'], $usr->ID ); ?>><?php echo esc_html( $usr->display_name ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td style="padding:12px 10px;vertical-align:top;text-align:right;">
                                    <div style="display:flex;flex-direction:column;gap:5px;align-items:flex-end;">
                                        <button type="button" class="button button-secondary jjwz-open-lead-tasks" data-lead-id="<?php echo $lead_id; ?>" data-lead-name="<?php echo esc_attr( $lead['name'] ); ?>" style="font-size:11px;padding:0 8px;height:24px;">
                                            📋 Tasks (<?php echo $task_count; ?>)
                                        </button>
                                        <button type="button" class="button button-secondary jjwz-open-lead-edit" data-lead='<?php echo esc_attr( wp_json_encode( $lead ) ); ?>' style="font-size:11px;padding:0 8px;height:24px;">
                                            ✍️ Edit Details
                                        </button>
                                        <button type="button" class="button button-link-delete jjwz-crm-delete-lead" data-lead-id="<?php echo $lead_id; ?>" style="font-size:11px;color:#e53e3e;text-decoration:none;padding:0;margin-top:2px;">
                                            🗑️ Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="6" style="text-align:center;padding:30px;color:#718096;">No leads match the selected filters. Submit contact forms or add a manual lead to test.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- MODAL: ADD MANUAL LEAD -->
            <div id="jjwz-add-lead-modal" style="display:none;position:fixed;z-index:99999;left:0;top:0;width:100%;height:100%;overflow:auto;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;">
                <div style="background:#fff;border-radius:8px;padding:25px;max-width:550px;width:90%;box-shadow:0 10px 25px rgba(0,0,0,0.15);position:relative;">
                    <span class="jjwz-close-modal" style="position:absolute;right:20px;top:15px;font-size:24px;cursor:pointer;color:#a0aec0;">&times;</span>
                    <h3 style="margin-top:0;font-family:Georgia,serif;color:#c9a96e;font-size:20px;border-bottom:1px solid #e2e8f0;padding-bottom:10px;">➕ Add Manual Lead</h3>
                    <form id="jjwz-manual-lead-form" style="margin-top:15px;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Full Name *</label>
                                <input type="text" name="name" required style="width:100%;height:32px;">
                            </div>
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Email Address *</label>
                                <input type="email" name="email" required style="width:100%;height:32px;">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Phone Number</label>
                                <input type="text" name="phone" style="width:100%;height:32px;">
                            </div>
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">City / Location</label>
                                <input type="text" name="city" style="width:100%;height:32px;">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Studio Brand</label>
                                <select name="brand" style="width:100%;height:32px;">
                                    <option value="JJ WeddingZ">JJ WeddingZ</option>
                                    <option value="The Baby StudioZ">The Baby StudioZ</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Core Service</label>
                                <select name="service" style="width:100%;height:32px;">
                                    <option value="">Select Service</option>
                                    <?php foreach ( $services_posts as $svc ) : ?>
                                        <option value="<?php echo esc_attr( $svc->post_title ); ?>"><?php echo esc_html( $svc->post_title ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Event Type</label>
                                <input type="text" name="event_type" placeholder="e.g. Wedding, Pre-Shoot" style="width:100%;height:32px;">
                            </div>
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Venue</label>
                                <input type="text" name="venue" placeholder="e.g. Oberoi, Taj" style="width:100%;height:32px;">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Package Selected</label>
                                <input type="text" name="package_selected" placeholder="e.g. Royal Heirloom" style="width:100%;height:32px;">
                            </div>
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Budget / Starting Price</label>
                                <input type="text" name="budget" placeholder="e.g. 1,50,000" style="width:100%;height:32px;">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Event Date</label>
                                <input type="text" name="event_date" placeholder="e.g. Dec 12, 2026" style="width:100%;height:32px;">
                            </div>
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Lead Status</label>
                                <select name="status" style="width:100%;height:32px;">
                                    <?php foreach ( $stages as $stg ) : ?>
                                        <option value="<?php echo esc_attr( $stg ); ?>"><?php echo esc_html( $stg ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div style="margin-bottom:15px;">
                            <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Notes</label>
                            <textarea name="message" rows="3" style="width:100%;"></textarea>
                        </div>
                        <div style="text-align:right;border-top:1px solid #e2e8f0;padding-top:12px;">
                            <button type="button" class="button jjwz-close-modal-btn">Cancel</button>
                            <button type="submit" class="button button-primary" style="background:#c9a96e;border-color:#c9a96e;">Save Lead</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL: EDIT LEAD DETAILS -->
            <div id="jjwz-edit-lead-modal" style="display:none;position:fixed;z-index:99999;left:0;top:0;width:100%;height:100%;overflow:auto;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;">
                <div style="background:#fff;border-radius:8px;padding:25px;max-width:550px;width:90%;box-shadow:0 10px 25px rgba(0,0,0,0.15);position:relative;">
                    <span class="jjwz-close-modal" style="position:absolute;right:20px;top:15px;font-size:24px;cursor:pointer;color:#a0aec0;">&times;</span>
                    <h3 style="margin-top:0;font-family:Georgia,serif;color:#c9a96e;font-size:20px;border-bottom:1px solid #e2e8f0;padding-bottom:10px;">✍️ Edit Lead Details</h3>
                    <form id="jjwz-edit-lead-form" style="margin-top:15px;">
                        <input type="hidden" name="lead_id">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Full Name *</label>
                                <input type="text" name="name" required style="width:100%;height:32px;">
                            </div>
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Email Address *</label>
                                <input type="email" name="email" required style="width:100%;height:32px;">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Phone Number</label>
                                <input type="text" name="phone" style="width:100%;height:32px;">
                            </div>
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">City</label>
                                <input type="text" name="city" style="width:100%;height:32px;">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Studio Brand</label>
                                <select name="brand" style="width:100%;height:32px;">
                                    <option value="JJ WeddingZ">JJ WeddingZ</option>
                                    <option value="The Baby StudioZ">The Baby StudioZ</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Core Service</label>
                                <select name="service" style="width:100%;height:32px;">
                                    <option value="">Select Service</option>
                                    <?php foreach ( $services_posts as $svc ) : ?>
                                        <option value="<?php echo esc_attr( $svc->post_title ); ?>"><?php echo esc_html( $svc->post_title ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Event Type</label>
                                <input type="text" name="event_type" style="width:100%;height:32px;">
                            </div>
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Venue</label>
                                <input type="text" name="venue" style="width:100%;height:32px;">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Package Selected</label>
                                <input type="text" name="package_selected" style="width:100%;height:32px;">
                            </div>
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Budget</label>
                                <input type="text" name="budget" style="width:100%;height:32px;">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Event Date</label>
                                <input type="text" name="event_date" style="width:100%;height:32px;">
                            </div>
                            <div>
                                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Lead Status</label>
                                <select name="status" style="width:100%;height:32px;">
                                    <?php foreach ( $stages as $stg ) : ?>
                                        <option value="<?php echo esc_attr( $stg ); ?>"><?php echo esc_html( $stg ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div style="margin-bottom:15px;">
                            <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Notes</label>
                            <textarea name="message" rows="3" style="width:100%;"></textarea>
                        </div>
                        <div style="text-align:right;border-top:1px solid #e2e8f0;padding-top:12px;">
                            <button type="button" class="button jjwz-close-modal-btn">Cancel</button>
                            <button type="submit" class="button button-primary" style="background:#c9a96e;border-color:#c9a96e;">Update Lead</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL: TASK MANAGEMENT -->
            <div id="jjwz-tasks-modal" style="display:none;position:fixed;z-index:99999;left:0;top:0;width:100%;height:100%;overflow:auto;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;">
                <div style="background:#fff;border-radius:8px;padding:25px;max-width:650px;width:95%;box-shadow:0 10px 25px rgba(0,0,0,0.15);position:relative;">
                    <span class="jjwz-close-modal" style="position:absolute;right:20px;top:15px;font-size:24px;cursor:pointer;color:#a0aec0;">&times;</span>
                    <h3 style="margin-top:0;font-family:Georgia,serif;color:#c9a96e;font-size:20px;border-bottom:1px solid #e2e8f0;padding-bottom:10px;">📋 Task Management: <span id="jjwz-modal-lead-name" style="color:#2d3748;font-weight:400;">Lead</span></h3>
                    
                    <!-- Quick Add Task -->
                    <form id="jjwz-add-task-form" style="background:#f7fafc;padding:12px;border:1px solid #e2e8f0;border-radius:6px;margin-top:15px;display:grid;grid-template-columns:2fr 1.2fr 1fr 1fr auto;gap:8px;align-items:flex-end;">
                        <input type="hidden" name="lead_id" id="jjwz-tasks-lead-id">
                        <div>
                            <label style="font-size:11px;font-weight:600;display:block;margin-bottom:4px;">Task Description *</label>
                            <input type="text" name="task_title" required placeholder="e.g. Follow up on proposal" style="width:100%;height:28px;font-size:12px;">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:600;display:block;margin-bottom:4px;">Assign To</label>
                            <select name="assigned_to" style="width:100%;height:28px;font-size:12px;">
                                <option value="0">Unassigned</option>
                                <?php foreach ( $team_users as $usr ) : ?>
                                    <option value="<?php echo $usr->ID; ?>"><?php echo esc_html( $usr->display_name ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:600;display:block;margin-bottom:4px;">Due Date</label>
                            <input type="date" name="due_date" style="width:100%;height:28px;font-size:11px;">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:600;display:block;margin-bottom:4px;">Priority</label>
                            <select name="priority" style="width:100%;height:28px;font-size:12px;">
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>
                        <button type="submit" class="button button-primary" style="height:28px;background:#3182ce;border-color:#3182ce;font-size:12px;">Add</button>
                    </form>

                    <!-- Tasks List -->
                    <div style="margin-top:20px;max-height:280px;overflow-y:auto;">
                        <table class="wp-list-table widefat fixed striped" style="border:none;">
                            <thead>
                                <tr style="background:#edf2f7;">
                                    <th style="width:40px;padding:8px 5px;">Done</th>
                                    <th style="padding:8px 5px;">Task</th>
                                    <th style="padding:8px 5px;width:100px;">Assignee</th>
                                    <th style="padding:8px 5px;width:90px;">Due Date</th>
                                    <th style="padding:8px 5px;width:80px;">Priority</th>
                                    <th style="width:50px;padding:8px 5px;text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="jjwz-tasks-list-body">
                                <tr><td colspan="6" style="text-align:center;padding:20px;color:#a0aec0;">Select a lead to manage tasks.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /* ─── AJAX Handlers — LEAD MANAGEMENT ───────────────────────────────── */

    public function handle_add_manual_lead(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Unauthorized access.' ], 403 );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'jjwz_leads';

        $name       = sanitize_text_field( $_POST['name'] ?? '' );
        $email      = sanitize_email( $_POST['email'] ?? '' );
        $phone      = sanitize_text_field( $_POST['phone'] ?? '' );
        $city       = sanitize_text_field( $_POST['city'] ?? '' );
        $brand      = sanitize_text_field( $_POST['brand'] ?? 'JJ WeddingZ' );
        $service    = sanitize_text_field( $_POST['service'] ?? '' );
        $event_type = sanitize_text_field( $_POST['event_type'] ?? '' );
        $venue      = sanitize_text_field( $_POST['venue'] ?? '' );
        $pkg        = sanitize_text_field( $_POST['package_selected'] ?? '' );
        $budget     = sanitize_text_field( $_POST['budget'] ?? '0' );
        $event_date = sanitize_text_field( $_POST['event_date'] ?? '' );
        $status     = sanitize_text_field( $_POST['status'] ?? 'New Lead' );
        $message    = sanitize_textarea_field( $_POST['message'] ?? '' );

        if ( empty( $name ) || empty( $email ) ) {
            wp_send_json_error( [ 'message' => 'Name and email are required.' ], 400 );
        }

        $inserted = $wpdb->insert( $table, [
            'name'             => $name,
            'email'            => $email,
            'phone'            => $phone,
            'city'             => $city,
            'brand'            => $brand,
            'service'          => $service,
            'event_type'       => $event_type,
            'venue'            => $venue,
            'package_selected' => $pkg,
            'budget'           => $budget,
            'event_date'       => $event_date,
            'status'           => $status,
            'message'          => $message,
            'source'           => 'Manual Entry',
            'created_at'       => current_time( 'mysql' )
        ] );

        if ( ! $inserted ) {
            wp_send_json_error( [ 'message' => 'Database error inserting lead.' ], 500 );
        }

        // Trigger WhatsApp automated notification hook for manual lead if applicable
        do_action( 'jjwz_new_lead', $wpdb->insert_id, [
            'name'    => $name,
            'phone'   => $phone,
            'service' => $service,
            'brand'   => $brand
        ] );

        wp_send_json_success( [ 'message' => 'Lead created successfully!' ] );
    }

    public function handle_update_lead(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Unauthorized access.' ], 403 );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'jjwz_leads';

        $lead_id = (int) ( $_POST['lead_id'] ?? 0 );
        if ( ! $lead_id ) {
            wp_send_json_error( [ 'message' => 'Invalid lead ID.' ], 400 );
        }

        $fields = [];
        $formats = [];

        // Check columns that were submitted
        if ( isset( $_POST['status'] ) ) {
            $fields['status'] = sanitize_text_field( $_POST['status'] );
            $formats[] = '%s';
        }
        if ( isset( $_POST['assigned_user'] ) ) {
            $fields['assigned_user'] = (int) $_POST['assigned_user'];
            $formats[] = '%d';
        }
        if ( isset( $_POST['follow_up_date'] ) ) {
            $follow_date = sanitize_text_field( $_POST['follow_up_date'] );
            $fields['follow_up_date'] = empty( $follow_date ) ? null : $follow_date;
            $formats[] = '%s';
        }
        if ( isset( $_POST['name'] ) ) {
            $fields['name'] = sanitize_text_field( $_POST['name'] );
            $formats[] = '%s';
        }
        if ( isset( $_POST['email'] ) ) {
            $fields['email'] = sanitize_email( $_POST['email'] );
            $formats[] = '%s';
        }
        if ( isset( $_POST['phone'] ) ) {
            $fields['phone'] = sanitize_text_field( $_POST['phone'] );
            $formats[] = '%s';
        }
        if ( isset( $_POST['city'] ) ) {
            $fields['city'] = sanitize_text_field( $_POST['city'] );
            $formats[] = '%s';
        }
        if ( isset( $_POST['brand'] ) ) {
            $fields['brand'] = sanitize_text_field( $_POST['brand'] );
            $formats[] = '%s';
        }
        if ( isset( $_POST['service'] ) ) {
            $fields['service'] = sanitize_text_field( $_POST['service'] );
            $formats[] = '%s';
        }
        if ( isset( $_POST['event_type'] ) ) {
            $fields['event_type'] = sanitize_text_field( $_POST['event_type'] );
            $formats[] = '%s';
        }
        if ( isset( $_POST['venue'] ) ) {
            $fields['venue'] = sanitize_text_field( $_POST['venue'] );
            $formats[] = '%s';
        }
        if ( isset( $_POST['package_selected'] ) ) {
            $fields['package_selected'] = sanitize_text_field( $_POST['package_selected'] );
            $formats[] = '%s';
        }
        if ( isset( $_POST['budget'] ) ) {
            $fields['budget'] = sanitize_text_field( $_POST['budget'] );
            $formats[] = '%s';
        }
        if ( isset( $_POST['event_date'] ) ) {
            $fields['event_date'] = sanitize_text_field( $_POST['event_date'] );
            $formats[] = '%s';
        }
        if ( isset( $_POST['message'] ) ) {
            $fields['message'] = sanitize_textarea_field( $_POST['message'] );
            $formats[] = '%s';
        }

        if ( empty( $fields ) ) {
            wp_send_json_error( [ 'message' => 'No fields to update.' ], 400 );
        }

        // Get previous status to check if it has transitioned
        $old_status = $wpdb->get_var( $wpdb->prepare( "SELECT status FROM {$table} WHERE id = %d", $lead_id ) );

        $updated = $wpdb->update( $table, $fields, [ 'id' => $lead_id ], $formats, [ '%d' ] );

        if ( $updated !== false ) {
            // Trigger Stage Update WhatsApp Automation hook if stage has changed
            if ( isset( $fields['status'] ) && $fields['status'] !== $old_status ) {
                do_action( 'jjwz_lead_stage_updated', $lead_id, $fields['status'], $old_status );
            }
            wp_send_json_success( [ 'message' => 'Lead updated successfully!' ] );
        } else {
            wp_send_json_error( [ 'message' => 'Database error updating lead.' ], 500 );
        }
    }

    public function handle_delete_lead(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Unauthorized access.' ], 403 );
        }

        global $wpdb;
        $leads_table = $wpdb->prefix . 'jjwz_leads';
        $tasks_table = $wpdb->prefix . 'jjwz_tasks';

        $lead_id = (int) ( $_POST['lead_id'] ?? 0 );
        if ( ! $lead_id ) {
            wp_send_json_error( [ 'message' => 'Invalid lead ID.' ], 400 );
        }

        // Delete lead
        $deleted = $wpdb->delete( $leads_table, [ 'id' => $lead_id ], [ '%d' ] );
        
        // Cascade delete tasks
        $wpdb->delete( $tasks_table, [ 'lead_id' => $lead_id ], [ '%d' ] );

        if ( $deleted ) {
            wp_send_json_success( [ 'message' => 'Lead and its linked tasks deleted successfully!' ] );
        } else {
            wp_send_json_error( [ 'message' => 'Database error deleting lead.' ], 500 );
        }
    }

    /* ─── AJAX Handlers — CRM TASK MANAGEMENT ───────────────────────────── */

    public function handle_get_lead_tasks(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Unauthorized access.' ], 403 );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'jjwz_tasks';
        $lead_id = (int) ( $_POST['lead_id'] ?? 0 );

        if ( ! $lead_id ) {
            wp_send_json_error( [ 'message' => 'Invalid lead ID.' ], 400 );
        }

        $tasks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE lead_id = %d ORDER BY created_at DESC", $lead_id ), ARRAY_A ) ?: [];
        wp_send_json_success( [ 'tasks' => $this->format_tasks_list( $tasks ) ] );
    }

    public function handle_add_lead_task(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Unauthorized access.' ], 403 );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'jjwz_tasks';

        $lead_id     = (int) ( $_POST['lead_id'] ?? 0 );
        $task_title  = sanitize_text_field( $_POST['task_title'] ?? '' );
        $assigned_to = (int) ( $_POST['assigned_to'] ?? 0 );
        $priority    = sanitize_text_field( $_POST['priority'] ?? 'Medium' );
        $due_date    = sanitize_text_field( $_POST['due_date'] ?? '' );

        if ( ! $lead_id || empty( $task_title ) ) {
            wp_send_json_error( [ 'message' => 'Lead ID and Task Title are required.' ], 400 );
        }

        $inserted = $wpdb->insert( $table, [
            'lead_id'     => $lead_id,
            'task_title'  => $task_title,
            'assigned_to' => $assigned_to,
            'priority'    => $priority,
            'due_date'    => empty( $due_date ) ? null : $due_date,
            'status'      => 'Pending',
            'created_at'  => current_time( 'mysql' )
        ] );

        if ( $inserted ) {
            // Retrieve list of updated tasks
            $tasks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE lead_id = %d ORDER BY created_at DESC", $lead_id ), ARRAY_A ) ?: [];
            wp_send_json_success( [ 'message' => 'Task created!', 'tasks' => $this->format_tasks_list( $tasks ) ] );
        } else {
            wp_send_json_error( [ 'message' => 'Database error creating task.' ], 500 );
        }
    }

    public function handle_toggle_lead_task(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Unauthorized access.' ], 403 );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'jjwz_tasks';

        $task_id = (int) ( $_POST['task_id'] ?? 0 );
        $status  = sanitize_text_field( $_POST['status'] ?? 'Pending' );

        if ( ! $task_id ) {
            wp_send_json_error( [ 'message' => 'Invalid task.' ], 400 );
        }

        $updated = $wpdb->update( $table, [ 'status' => $status ], [ 'id' => $task_id ], [ '%s' ], [ '%d' ] );

        if ( $updated !== false ) {
            $lead_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT lead_id FROM {$table} WHERE id = %d", $task_id ) );
            $tasks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE lead_id = %d ORDER BY created_at DESC", $lead_id ), ARRAY_A ) ?: [];
            wp_send_json_success( [ 'message' => 'Task updated!', 'tasks' => $this->format_tasks_list( $tasks ), 'lead_id' => $lead_id ] );
        } else {
            wp_send_json_error( [ 'message' => 'Database error toggling task status.' ], 500 );
        }
    }

    public function handle_delete_lead_task(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Unauthorized access.' ], 403 );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'jjwz_tasks';

        $task_id = (int) ( $_POST['task_id'] ?? 0 );
        if ( ! $task_id ) {
            wp_send_json_error( [ 'message' => 'Invalid task.' ], 400 );
        }

        $lead_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT lead_id FROM {$table} WHERE id = %d", $task_id ) );
        $deleted = $wpdb->delete( $table, [ 'id' => $task_id ], [ '%d' ] );

        if ( $deleted ) {
            $tasks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE lead_id = %d ORDER BY created_at DESC", $lead_id ), ARRAY_A ) ?: [];
            wp_send_json_success( [ 'message' => 'Task deleted!', 'tasks' => $this->format_tasks_list( $tasks ), 'lead_id' => $lead_id ] );
        } else {
            wp_send_json_error( [ 'message' => 'Database error deleting task.' ], 500 );
        }
    }

    /**
     * Helper to return tasks list JSON array
     */
    private function format_tasks_list( array $tasks ): array {
        $formatted = [];
        foreach ( $tasks as $t ) {
            $user_name = 'Unassigned';
            if ( $t['assigned_to'] ) {
                $userdata = get_userdata( $t['assigned_to'] );
                if ( $userdata ) { $user_name = $userdata->display_name; }
            }
            $formatted[] = [
                'id'          => $t['id'],
                'lead_id'     => $t['lead_id'],
                'task_title'  => esc_html( $t['task_title'] ),
                'assigned_to' => $t['assigned_to'],
                'assignee'    => esc_html( $user_name ),
                'due_date'    => $t['due_date'] ? esc_html( date( 'M j, Y', strtotime( $t['due_date'] ) ) ) : '—',
                'priority'    => esc_html( $t['priority'] ),
                'status'      => esc_html( $t['status'] )
            ];
        }
        return $formatted;
    }
}
new JJWZ_CRM_Manager();
