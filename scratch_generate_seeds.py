import os
import json
import csv

# Create seeds directory
seeds_dir = os.path.join(os.path.dirname(__file__), 'jjw-core', 'assets', 'seeds')
os.makedirs(seeds_dir, exist_ok=True)

services = [
    {
        'name': 'Wedding Photography',
        'slug': 'wedding-photography',
        'icon': '💍',
        'focus_keywords': 'wedding photographer, wedding photography, luxury wedding photography',
        'seo_title': 'Luxury Wedding Photography by {brand_name} | Editorial Wedding Stories',
        'meta_description': 'Document your wedding story with luxury, editorial elegance. {brand_name} captures unscripted emotions and grand celebrations across Amritsar and Delhi NCR.',
        'short_description': 'Luxury editorial wedding photography documenting unscripted emotion, structural compositions, and authentic love stories with a strict 100% Identity Promise.',
        'brand': 'both',
        'price': '₹1,50,000',
        'features': [
            'Full day coverage of all primary wedding ceremonies and rituals',
            'Lead documentary photographer and secondary candid shooter',
            'High-resolution edited digital gallery with private online access',
            'Full dual-card real-time data redundancy across all camera units',
            'Pre-wedding consultation and conceptual planning session',
            'Bespoke hand-crafted fine-art wedding album (40 pages)'
        ],
        'process': [
            'Conceptual Consultation & Storyboarding',
            'On-Location Setup & Multi-Angle Coverage',
            'Real-Time Dual-Card Safe Backup',
            'Meticulous Culling & Editorial Color Grading',
            'Private Digital Access & Fine-Art Album Delivery'
        ],
        'keywords_context': ['wedding', 'candid moments', 'bride', 'groom', 'celebration', 'ritual', 'lehenga', 'ballroom']
    },
    {
        'name': 'Pre Wedding Photography',
        'slug': 'pre-wedding-photography',
        'icon': '📸',
        'focus_keywords': 'pre wedding shoot, pre wedding photography, creative pre wedding shoot',
        'seo_title': 'Creative Pre Wedding Photography | Fine-Art Love Stories by {brand_name}',
        'meta_description': 'Tell your unique love story with a creative pre-wedding photoshoot. Styled conceptual sessions in heritage backdrops and luxury environments.',
        'short_description': 'Bespoke conceptual pre-wedding shoots set in historic backdrops and natural light landscapes, capturing your visual chemistry with editorial elegance.',
        'brand': 'both',
        'price': '₹75,000',
        'features': [
            'Multi-location scouting and customized shoot planning',
            'Up to three curated outfit modifications and styling consults',
            'High-resolution retouched digital files via online gallery',
            'Bespoke visual theme creation matching your relationship narrative',
            'Cinematic 9:16 vertical reel optimized for mobile platforms',
            'Behind-the-scenes video highlight compilation'
        ],
        'process': [
            'Theme Conception & Wardrobe Planning',
            'Location Selection & Permit Coordination',
            'Bespoke Golden Hour Shoot Execution',
            'Fine-Art Retouching & Color Profiling',
            'Digital Gallery Delivery'
        ],
        'keywords_context': ['pre-wedding', 'chemistry', 'couple', 'scouting', 'wardrobe', 'golden hour', 'poses', 'narrative']
    },
    {
        'name': 'Destination Wedding Photography',
        'slug': 'destination-wedding-photography',
        'icon': '✈️',
        'focus_keywords': 'destination wedding photographer, destination wedding photography, luxury destination wedding',
        'seo_title': 'Luxury Destination Wedding Photographer | {brand_name} Global',
        'meta_description': 'Award-winning destination wedding photography. We travel globally from Rajasthan palaces to beachside retreats to capture your luxury wedding.',
        'short_description': 'Globally available destination wedding photography capturing your celebration in historic palaces, tropical beaches, or alpine retreats with standard-setting quality.',
        'brand': 'both',
        'price': '₹2,50,000',
        'features': [
            'Global travel availability with experienced multi-lingual crew',
            'Complete multi-day event coverage (welcome party to brunch)',
            'Comprehensive drone aerial mapping and establishing shots',
            'Dual-card data redundancy and encrypted cloud backup protocols',
            'Two luxury hand-bound leather albums with custom engraving',
            'Complete high-resolution digital delivery within 8 weeks'
        ],
        'process': [
            'Travel Logistics & Site Reconnaissance',
            'Multi-Day Schedule Alignment',
            'Epic Scale Aerial & Ground Coverage',
            'Daily Secure Data Backups',
            'Comprehensive Post-Production & Album Sourcing'
        ],
        'keywords_context': ['destination', 'palace', 'resort', 'travel', 'aerial', 'multi-day', 'heritage', 'global']
    },
    {
        'name': 'Wedding Cinematography',
        'slug': 'wedding-cinematography',
        'icon': '🎥',
        'focus_keywords': 'wedding cinematography, wedding filmmaker, wedding film, cinematic wedding',
        'seo_title': 'Cinematic Wedding Films & Documentaries | {brand_name} Cinema',
        'meta_description': 'Breathtaking cinematic wedding films shot on Sony FX3 systems. Editorial pacing, Dolby audio, and emotional storytelling by {brand_name}.',
        'short_description': 'High-end cinema-grade wedding cinematography capturing sound, movement, and emotion with Sony FX3 systems and a proprietary editing formula.',
        'brand': 'both',
        'price': '₹2,00,000',
        'features': [
            'Sony FX3 cinema line camera systems and prime lens kits',
            'Bespoke audio capture of vows, speeches, and ambient sound',
            'Cinematic 3-5 minute teaser trailer for social distribution',
            'Full-length 15-20 minute editorial wedding documentary film',
            'Drone aerial footage for establishing venue context',
            'Premium color grading using custom cinema-grade LUTs'
        ],
        'process': [
            'Sound & Audio Design Planning',
            'On-Set Camera & Lighting Operations',
            'Daily Sound-Synced Backups',
            'Narrative Editing & Score Selection',
            'Color Grading & Audio Mastering'
        ],
        'keywords_context': ['cinematography', 'focal length', 'teaser', 'documentary', 'audio', 'LUTs', 'movement', 'pacing']
    },
    {
        'name': 'Maternity Photoshoot',
        'slug': 'maternity-photoshoot',
        'icon': '🤰',
        'focus_keywords': 'maternity photoshoot, pregnancy photography, maternity photographer',
        'seo_title': 'Luxury Maternity Photoshoot | Fine-Art Pregnancy Portraits',
        'meta_description': 'Celebrate the miracle of life with a luxury maternity photoshoot by {brand_name}. Studio and outdoor sessions styled for ultimate comfort.',
        'short_description': 'Exquisite maternity portraits designed with comfort and safety in mind, capturing the beautiful glow of pregnancy in studio or natural landscapes.',
        'brand': 'both',
        'price': '₹60,000',
        'features': [
            'Access to our designer gown collection and silk draperies',
            'Professional hair and soft makeup coordination services',
            'Private sanitized studio environment with adjustable climate control',
            'Comfort-first posing guidance tailored to your trimester',
            'High-resolution retouched digital gallery with digital sharing',
            'Complimentary partner and sibling inclusion in portraits'
        ],
        'process': [
            'Wardrobe Styling & Makeup Consultation',
            'Studio Sanitization & Climate Tuning',
            'Relaxed Pacing Shoot Session',
            'Soft Glow Retouching & Light Enhancement',
            'Heirloom Portrait Delivery'
        ],
        'keywords_context': ['maternity', 'pregnancy', 'gown', 'glow', 'sanitized', 'comfort', 'portrait', 'maternal']
    },
    {
        'name': 'Newborn Photoshoot',
        'slug': 'newborn-photoshoot',
        'icon': '👶',
        'focus_keywords': 'newborn photoshoot, newborn photography, baby photographer',
        'seo_title': 'Fine-Art Newborn Photoshoot | Safe Baby Portraiture',
        'meta_description': 'Document the first days of your baby\'s life safely. Certified newborn photoshoot in a sanitized, climate-controlled studio environment.',
        'short_description': 'Certified, safety-first newborn sessions capturing sleepy, peaceful poses in a medical-grade sanitized, climate-controlled studio.',
        'brand': 'both',
        'price': '₹65,000',
        'features': [
            'Safety-certified newborn posing specialist handling the baby',
            'Complete access to organic sanitized wraps and props',
            'Strict sanitization and heating protocols (26–28°C environment)',
            'Patience-first scheduling allowing ample feeding and sleeping breaks',
            'Delicately retouched high-resolution digital images',
            'Parent and newborn classic connection portrait sets'
        ],
        'process': [
            'Medical-Grade Studio Prep & Pre-heating',
            'Patience-Led Feeding & Settling Session',
            'Safe Certified Sleeping Poses',
            'Delicate Blemish-Free Editorial Retouching',
            'Private Digital Story Delivery'
        ],
        'keywords_context': ['newborn', 'wraps', 'props', 'sanitized', 'safety-certified', 'asleep', 'delicate', 'heirloom']
    },
    {
        'name': 'Baby Photoshoot',
        'slug': 'baby-photoshoot',
        'icon': '🧸',
        'focus_keywords': 'baby photoshoot, baby photography, milestone baby photoshoot',
        'seo_title': 'Milestone Baby Photoshoot | Capturing Joyful Moments',
        'meta_description': 'Capture your baby\'s growing personality. Playful milestone photoshoots celebrating sitting, crawling, and curious expressions.',
        'short_description': 'Joyful milestone baby photoshoots designed to capture crawls, sits, and sweet expressions in a sanitized, play-friendly environment.',
        'brand': 'both',
        'price': '₹50,000',
        'features': [
            'Access to whimsical, high-quality play props and toys',
            'Sanitized play area with soft mats and baby-safe lighting',
            'Patience-focused team expert in eliciting genuine baby smiles',
            'Dynamic action capture of crawls, sits, and milestone expressions',
            'High-resolution clean edited digital portraits',
            'Flexible scheduling around baby\'s optimal active hours'
        ],
        'process': [
            'Active Hour Scheduling',
            'Interactive Play Session Setup',
            'High-Speed Expression Capture',
            'Clean Color Profiling & Editing',
            'Joyful Album/Digital Delivery'
        ],
        'keywords_context': ['baby', 'milestone', 'crawling', 'smiles', 'toys', 'sanitized', 'playful', 'expression']
    },
    {
        'name': 'Cake Smash Photoshoot',
        'slug': 'cake-smash-photoshoot',
        'icon': '🎂',
        'focus_keywords': 'cake smash photoshoot, cake smash photography, first birthday photoshoot',
        'seo_title': 'Fun Cake Smash Photoshoot | First Birthday Portraits',
        'meta_description': 'Celebrate the first birthday milestone with a fun, messy cake smash photoshoot. Customizable themes and sanitized setups.',
        'short_description': 'A fun, sensory-rich first birthday milestone session featuring custom theme setups, a beautiful cake, and messy splash bath photography.',
        'brand': 'both',
        'price': '₹55,000',
        'features': [
            'Bespoke backdrop styling matching your choice of theme',
            'Coordination of baby-safe organic smash cake options',
            'Post-smash bubble splash bath photography session',
            'Sanitized setups and immediate warm towel cleanup service',
            'High-resolution action-packed edited digital images',
            'Custom first-birthday collage digital template'
        ],
        'process': [
            'Theme & Backdrop Custom Styling',
            'Smash Time Messy Interaction Capture',
            'Splash Time Warm Tub Bubble Session',
            'Cleanup & Warm Towel Transition',
            'High-Speed Fun Retouching & Delivery'
        ],
        'keywords_context': ['cake smash', 'birthday', 'splash', 'bubble bath', 'messy', 'smash', 'theme', 'sensory']
    },
    {
        'name': 'First Birthday Photoshoot',
        'slug': 'first-birthday-photoshoot',
        'icon': '🎈',
        'focus_keywords': 'first birthday photoshoot, birthday photography, baby first birthday',
        'seo_title': 'First Birthday Photoshoot | Timeless Family Celebrations',
        'meta_description': 'Document the monumental first birthday milestone. Timeless portraits of your baby combined with warm family legacy groupings.',
        'short_description': 'Bespoke first birthday portraits combining elegant baby solos, whimsical balloons, and heartfelt multi-generational family groupings.',
        'brand': 'both',
        'price': '₹50,000',
        'features': [
            'Customized birthday set styling (balloons, letters, number 1)',
            'Legacy family portrait session with parents and grandparents',
            'High-resolution clean edited digital images via gallery',
            'Curated milestone portrait setup showcasing growth stages',
            'Bespoke birthday highlight video reel (30 seconds)',
            'Keepsake digital invitation card layout included'
        ],
        'process': [
            'Milestone Stage Conceptual Design',
            'Solo Portrait Set Capture',
            'Family Legacy Integration Session',
            'Festive Editing & Color Optimization',
            'Milestone Gallery Delivery'
        ],
        'keywords_context': ['first birthday', 'balloons', 'number one', 'grandparents', 'legacy', 'celebration', 'growth', 'milestone']
    },
    {
        'name': 'Kids Photography',
        'slug': 'kids-photography',
        'icon': '🪁',
        'focus_keywords': 'kids photography, child portraiture, children photoshoot',
        'seo_title': 'Editorial Kids Photography | Natural Child Portraiture',
        'meta_description': 'Natural, unforced children\'s portraiture. {brand_name} captures your child\'s genuine spirit, laughter, and curious eyes.',
        'short_description': 'Natural and unforced children’s portraiture documenting the genuine spirit, laughter, and creative curiosity of your child.',
        'brand': 'both',
        'price': '₹45,000',
        'features': [
            'Play-based, non-directive photography approach for kids',
            'Indoor styled studio setup or dynamic outdoor play session',
            'High-resolution edited digital portraits via web portal',
            'Patience-focused pacing accommodating active children',
            'Stunning black and white editorial portrait sets',
            'Custom digital keepsakes showcasing child\'s interests'
        ],
        'process': [
            'Rapport Building & Play Integration',
            'Natural Non-Directive Portrait Session',
            'Culling for Genuine Candid Expressions',
            'Editorial Contrast & Color Balance',
            'Keepsake Digital Delivery'
        ],
        'keywords_context': ['kids', 'children', 'candid', 'laughter', 'play-based', 'expression', 'black and white', 'curiosity']
    },
    {
        'name': 'Family Photography',
        'slug': 'family-photography',
        'icon': '🏡',
        'focus_keywords': 'family photography, family portrait, legacy family photoshoot',
        'seo_title': 'Legacy Family Photography | Timeless Generations',
        'meta_description': 'Create a visual legacy with a premium family photoshoot. Beautiful multi-generational portraits to treasure for lifetimes.',
        'short_description': 'Heartfelt multi-generational family portrait sessions capturing your shared laughter and legacy in our studio or your estate gardens.',
        'brand': 'both',
        'price': '₹60,000',
        'features': [
            'Legacy groupings (grandparents, siblings, nuclear units)',
            'Pre-shoot wardrobe guidelines for color harmony',
            'On-location garden shoot or indoor editorial studio setup',
            'High-resolution print-ready edited digital portraits',
            'Strict compliance with our face-accuracy identity commitment',
            'Large-format canvas print layout template included'
        ],
        'process': [
            'Family Unit & Wardrobe Co-ordination',
            'Structured Legacy Grouping Setup',
            'Dynamic Interaction & Laughter Prompts',
            'Timeless Legacy Retouching & Editing',
            'Print-Ready Digital Gallery Delivery'
        ],
        'keywords_context': ['family', 'generations', 'legacy', 'wardrobe', 'harmony', 'grouping', 'parents', 'children']
    },
    {
        'name': 'Anniversary Photoshoot',
        'slug': 'anniversary-photoshoot',
        'icon': '🥂',
        'focus_keywords': 'anniversary photoshoot, couple photography, milestone anniversary',
        'seo_title': 'Elegant Anniversary Photoshoot | Celebrating Milestones',
        'meta_description': 'Celebrate your love milestone. Timeless anniversary photoshoots reflecting on your journey with editorial visual grace.',
        'short_description': 'A sophisticated couple’s session celebrating your years together with editorial portraits, golden-hour light, and elegant styling.',
        'brand': 'both',
        'price': '₹65,000',
        'features': [
            'Styled editorial couple session with customized concepts',
            'Access to location scouting guides and outfit suggestions',
            'High-resolution edited digital portraits via online gallery',
            'Beautiful editorial layout print design template',
            'Fine-art canvas print layout (16x24 size equivalent)',
            'Short cinematic highlight slide reel'
        ],
        'process': [
            'Milestone Reflection Consultation',
            'Bespoke Concept & Wardrobe Styling',
            'Editorial Couple Session Execution',
            'Fine-Art Highlight Editing',
            'Digital & Canvas Legacy Delivery'
        ],
        'keywords_context': ['anniversary', 'milestone', 'couple', 'love story', 'styling', 'gold', 'timeless', 'editorial']
    },
    {
        'name': 'Couple Photoshoot',
        'slug': 'couple-photoshoot',
        'icon': '👩‍❤️‍👨',
        'focus_keywords': 'couple photoshoot, couple photography, romance photographer',
        'seo_title': 'Romance & Chemistry: Premium Couple Photoshoot',
        'meta_description': 'Document your relationship chemistry. Premium couple photoshoots capturing warm glances, deep bonds, and shared smiles.',
        'short_description': 'Romantic, naturally lit couple sessions capturing your visual chemistry and quiet glances in beautiful outdoor or indoor settings.',
        'brand': 'both',
        'price': '₹55,000',
        'features': [
            'Pre-shoot briefing and theme development planning',
            'Natural posing guidance ensuring comfortable chemistry',
            'High-resolution edited digital files with printing rights',
            'Outdoor golden-hour or intimate indoor lifestyle setting',
            'Bespoke digital gallery with sharing functionality',
            'Mobile-optimized photo portfolio booklet'
        ],
        'process': [
            'Chemistry & Vibe Consultation',
            'Scenic Location Walkthrough',
            'Soft Light Chemistry Session',
            'Gradients & Shadows Fine Editing',
            'Digital Album & Sharing Delivery'
        ],
        'keywords_context': ['couple', 'chemistry', 'romance', 'lifestyle', 'posing', 'scenic', 'intimate', 'connection']
    },
    {
        'name': 'Studio Photography',
        'slug': 'studio-photography',
        'icon': '🏛️',
        'focus_keywords': 'studio photography, professional portrait, studio photoshoot',
        'seo_title': 'Professional Studio Photography | Fine-Art Portraiture',
        'meta_description': 'Expert studio photography utilising premium light controls. High-fashion bridal solos, executive headshots, and editorial portfolios.',
        'short_description': 'Editorial studio sessions leveraging premium controlled lighting systems for high-fashion bridal portraits, kids solos, or legacy profiles.',
        'brand': 'both',
        'price': '₹40,000',
        'features': [
            'Controlled studio lighting setups (softboxes, beauty dishes)',
            'Multiple backdrops (classic grey, ivory, warm beige, black)',
            'High-resolution fully retouched digital files via portal',
            'Ideal for high-fashion bridal solos and fine-art portraits',
            'Complimentary styling advice and posing assistance',
            'Fast-tracked delivery options available'
        ],
        'process': [
            'Lighting & Backdrop Set Customization',
            'Technical Light-Meter Setup',
            'Editorial Portrait Session',
            'Advanced Blemish-Free Color Retouching',
            'Digital Portfolio Delivery'
        ],
        'keywords_context': ['studio', 'lighting', 'backdrop', 'softbox', 'headshot', 'profile', 'controlled', 'fashion']
    },
    {
        'name': 'Outdoor Photography',
        'slug': 'outdoor-photography',
        'icon': '🌳',
        'focus_keywords': 'outdoor photography, natural light shoot, outdoor photoshoot',
        'seo_title': 'Natural Light Outdoor Photography | Scenic Portraits',
        'meta_description': 'Capture your milestones outdoors. Natural light photography sessions in heritage structures, lush gardens, and scenic landscapes.',
        'short_description': 'Scenic outdoor photography utilizing golden-hour sunlight and natural framing for couples, maternity, and legacy family stories.',
        'brand': 'both',
        'price': '₹50,000',
        'features': [
            'On-location outdoor session in curated parks/estates',
            'Scouting report for solar positioning and shadows',
            'High-resolution clean edited digital files via gallery',
            'Ideal for lively family legacy pictures and active kids',
            'Natural posing prompts creating real motion frames',
            'Complimentary travel within city municipal limits'
        ],
        'process': [
            'Solar Schedule & Location Scouting',
            'Golden Hour Dynamic Setup',
            'Scenic Motion Portraiture Session',
            'Natural Contrast Color Correction',
            'Digital Scenic Gallery Delivery'
        ],
        'keywords_context': ['outdoor', 'scenic', 'sunlight', 'gardens', 'motion', 'travel', 'landscape', 'natural light']
    }
]

def generate_generic_text(name, kw, contexts):
    p1 = f"In the field of high-end visual documentation, the art of <strong>{name}</strong> represents a profound alignment of technical precision, creative vision, and emotional sensitivity. When undertaking a professional assignment, the modern digital creator must recognize that client expectations have shifted from basic documentative photography to an immersive, editorial narrative. A truly premium visual story does not merely record a timeline of events; it elevates everyday interactions, fleeting emotions, and subtle architectural spaces into an exquisite heirloom collection. At our luxury studios, we treat every single frame as a distinct canvas, utilizing advanced optical designs and strict capture guidelines to achieve this standard of visual excellence. We have designed a cohesive approach that balances soft, ambient lighting with crisp focus fields, ensuring that your final galleries feel noticeability premium, alive, and emotionally resonant."
    
    p2 = f"To achieve an editorial look that rivals international publications, the technical production phase must be managed with absolute diligence. Our professional gear lists feature dual-slot mirrorless systems, including the Nikon Z6 III and Sony FX3 cinema platforms, which capture immense dynamic range. We utilize prime lens systems with wide maximum apertures, enabling our crew to control focus fields tightly and produce smooth background separation. This technical depth is particularly vital during key moments of <strong>{name}</strong>, as it allows us to isolate subjects cleanly from busy visual backdrops. Beyond equipment, light coordination is the true benchmark of luxury photography. Whether we are utilizing the soft golden sun of early evening or employing our custom-balanced off-camera flash units, we balance highlight gradients carefully to preserve natural skin textures and color fidelity."
    
    p3 = "Our commitment to authenticity is represented by our 100% Identity Promise. In an era where artificial intelligence and synthetic filters are frequently used to modify human appearances, we have chosen a different path. We guarantee that your final portraits will be entirely free of digital face-swapping, artificial skin whitening, or computer-generated facial alterations. Our post-production pipeline focuses exclusively on clean color correction, highlight reclamation, and micro-contrast enhancement. This ensures that the individuals in our photographs look like themselves on their absolute best day — preserving their genuine identity, expressions, and natural beauty. Our clients trust us because they know their legacy albums will reflect their true visual identity, capturing their milestones with honesty and grace."
    
    p4 = "The planning phase is the foundation upon which all successful visual stories are built. Long before our crew arrives on set, we conduct thorough styling, conceptual coordination, and venue walkthroughs. During this stage, we analyze color stories, architectural angles, and light directions to formulate a definitive shoot plan. We believe that proper wardrobe coordination is essential for creating high-end imagery; we recommend neutral palettes, jewel tones, and fine fabrics that complement rather than clash with the environment. By aligning styling variables early, we allow the session day to flow with relaxed ease, giving the subjects the space to act naturally and enjoy the experience. This collaborative process ensures that there are no surprises, only beautiful, unforced moments that capture your true chemistry."
    
    p5 = f"During an active shoot session, our crew operates with structured discretion. Rather than directing subjects into rigid, uncomfortable poses, we use natural prompts that elicit genuine smiles, quiet glances, and authentic laughter. This approach is particularly effective for <strong>{name}</strong>, where emotional resonance is the ultimate goal. We observe local details, clothing textures, and light reflections, capturing the complete atmosphere of the event. Our team remains attentive and prepared, anticipating key moments to ensure they are recorded with perfect timing. By maintaining a calm, supportive presence, we enable our subjects to build rapport with the camera, yielding relaxed, editorial imagery that speaks volumes."
    
    p6 = "Post-production is where the raw files are crafted into their final, signature look. Our dedicated editing team reviews the complete set, culling duplicates to compile a cohesive, narrative sequence. We apply custom-built color profiles that enhance warm amber tones and rich dark gradients while maintaining perfect color balance. Every selected image undergoes careful highlight adjustments to ensure that the delicate details of wedding gowns, family heirlooms, or child smiles are visible. The final collection is uploaded to a secure, password-protected private gallery portal, allowing you to download high-resolution files and share them with loved ones easily. We also offer custom-designed fine-art albums, printed on archival cotton papers and hand-bound in leather, ensuring your milestones survive as physical legacies."
    
    p7 = f"Investing in professional photography is ultimately an investment in your family's visual legacy. As years pass, digital links may be updated, but a physical fine-art album remains a permanent centerpiece of your home. It becomes the tangible record that future generations will explore to connect with your story, trace their roots, and feel the love and joy of your milestones. Our dual-branch structure across Delhi NCR and Amritsar allows us to deliver this premium standard of service across Northern India with consistent operational excellence. We limit our seasonal bookings strictly to guarantee that every client receives our complete focus and creative energy, resulting in a bespoke product that represents the peak of modern luxury visual art."

    return "\n\n".join([p1, p2, p3, p4, p5, p6, p7])

def generate_city_text(name, city_name, contexts):
    is_amritsar = (city_name.lower() == 'amritsar')
    
    if is_amritsar:
        p1 = f"For families and couples seeking a premium <strong>{name} in Amritsar</strong>, the historical landscape of Punjab offers an unparalleled backdrop. Amritsar is not merely a location; it is a profound visual canvas rich with cultural weight, textured brick architecture, and golden solar gradients. When documenting milestones here, our Amritsar branch crew utilizes the unique geometry of heritage locations, including the fortress walls of Fort Gobindgarh, the rustic arches of old-city havelis, and the serene manicured grounds of Ram Bagh. The warm, terracotta brickwork and historic wooden doors of Punjab provide a natural color contrast when paired with modern, elegant wardrobe choices. By planning shoots around the sun's trajectory in these open environments, we leverage soft light to sculpt faces naturally, maintaining complete identity accuracy without the need for artificial filters."
        
        p2 = f"Executing high-end photography in Amritsar requires a deep understanding of local light behavior and peak seasonal timing. The golden hour in Amritsar is famously soft, delivering a warm amber glow that enhances skin tones and highlights clothing details beautifully. Our local team conducts detailed scouting of locations like Ranjit Avenue, heritage haveli estates, and luxury hotels such as Hyatt Regency Amritsar and Taj Swarna. This allows us to map lighting patterns and shadows in advance, ensuring our Nikon Z6 III and Sony FX3 systems are calibrated perfectly for the local environment. We schedule sessions during the early morning or late afternoon hours when the sun is low, avoiding the harsh overhead shadows of midday and providing our clients with a relaxed, comfortable experience."
        
        p3 = f"Our Amritsar studio is fully equipped to deliver the highest international standards of luxury photography. For maternity, newborn, and baby photoshoot sessions, we maintain a strictly sanitized, temperature-controlled environment that follows medical-grade hygiene protocols. All props, organic wraps, and clothing sets are thoroughly sanitized between sessions to ensure the complete safety of your little ones. Our local Amritsar team is dedicated to preserving the authentic spirit of Punjab, documenting family legacies, joyful milestones, and wedding celebrations with unretouched honesty. We invite you to visit our Ranjit Avenue studio to explore our physical fine-art albums and discuss how we can document your legacy story beautifully."
    else:
        p1 = f"The sprawling metropolitan landscape of Delhi NCR offers a dynamic mix of architectural eras, grand estates, and lush manicured parks, making it a spectacular canvas for <strong>{name} in Delhi NCR</strong>. From the clean, symmetrical geometry of Lutyens' Delhi and the artistic murals of the Lodhi Art District to the grand, high-ceiling ballrooms of Chanakyapuri and the sprawling farmhouses of Chattarpur, the visual choices are virtually limitless. Our Delhi NCR branch crew specializes in composing editorial portraits that utilize these diverse backdrops to tell sophisticated, modern stories. We select locations that provide ample physical depth, allowing us to use telephoto lenses to create a soft, creamy background blur that isolates our subjects beautifully against the city's lines."
        
        p2 = f"Operating a luxury photography house in the capital region requires exceptional logistical planning and technical adaptiveness. The ambient light of Delhi NCR is highly variable, affected by seasonal changes and metropolitan textures. To capture pristine imagery, our Delhi crew leverages a combination of natural golden-hour sunlight and advanced, color-balanced off-camera lighting arrays. When shooting inside elite venues like Taj Palace Delhi, The Leela Ambience Gurugram, or ITC Maurya, we sync our flash units to match the ambient warm light of the ballrooms, ensuring skin tones remain natural and free of distracting color casts. This meticulous approach to lighting allows us to deliver high-resolution files that maintain incredible detail across highlights and deep shadows alike."
        
        p3 = f"Our Delhi NCR branch serves Gurugram, Noida, South Delhi, Aerocity, and surrounding areas, delivering a seamless, premium client experience from the initial consultation to the final gallery delivery. Whether you are hosting a grand wedding gala at a luxury farmhouse or scheduling an intimate milestone photoshoot in our professional studio, we apply the same strict standards of safety, privacy, and artistic integrity. We offer a strict 100% Identity Promise, guaranteeing your photographs will feature zero artificial face-swapping or skin whitening, preserving your true essence. Contact our Delhi NCR studio today to discuss custom shoot concepts, verify date availability, and begin planning your visual legacy."

    return "\n\n".join([p1, p2, p3])

json_data = []
csv_headers = [
    'Service Name', 'Slug', 'Service Icon', 'Brand Context', 'Starting Price', 'Focus Keywords', 
    'SEO Title', 'Meta Description', 'Short Description', 'Full Generic Content', 
    'Features List', 'Process Steps', 'Amritsar Title', 'Amritsar Meta Description', 
    'Amritsar Content', 'Amritsar CTA', 'Amritsar FAQs',
    'Delhi Title', 'Delhi Meta Description', 'Delhi Content', 'Delhi CTA', 'Delhi FAQs',
    'Packages JSON'
]
csv_rows = []

for s in services:
    name = s['name']
    slug = s['slug']
    icon = s['icon']
    kw = s['focus_keywords']
    seo_title = s['seo_title']
    meta_desc = s['meta_description']
    short_desc = s['short_description']
    brand = s['brand']
    price = s['price']
    
    generic_content = generate_generic_text(name, kw, s['keywords_context'])
    
    amritsar_title = f"Luxury {name} in Amritsar | Fine-Art Photographer"
    amritsar_meta = f"Document your {name.lower()} in Amritsar. {{brand_name}} offers premium editorial photography capturing authentic moments across Punjab."
    amritsar_content = generate_city_text(name, 'Amritsar', s['keywords_context'])
    amritsar_cta = f"<p>Ready to capture your {name.lower()} in the heritage city of Amritsar? Connect with our Ranjit Avenue studio today via WhatsApp or our booking form to check date availability and review packages.</p>"
    
    delhi_title = f"Premium {name} Delhi NCR | Editorial Photography"
    delhi_meta = f"Looking for a luxury photographer for {name.lower()} in Delhi NCR? Contact {{brand_name}} for high-end portraits and cinematic films."
    delhi_content = generate_city_text(name, 'Delhi', s['keywords_context'])
    delhi_cta = f"<p>Plan your editorial {name.lower()} session in Delhi NCR. Get in touch with our metropolitan branch to discuss locations, custom styling guidelines, and package options.</p>"
    
    faq_topics = [
        {"Q": f"What is your turnaround time for final {name.lower()} photos?", "A": "We deliver a highlight gallery within 7 days of the shoot. The complete, fully edited digital collection is delivered within 6 to 8 weeks, ensuring high-end retouching standards."},
        {"Q": f"What is your policy on RAW / unedited {name.lower()} files?", "A": "To maintain our brand standards and editorial quality, we do not release raw or unedited files. Every photograph in your gallery is carefully culled, color-balanced, and polished."},
        {"Q": f"Do you travel for {name.lower()} sessions outside Delhi or Amritsar?", "A": "Yes, our team frequently travels across India and internationally for assignments. Travel and accommodation fees are quoted transparently during booking."},
        {"Q": f"How do you ensure data security during the {name.lower()} photoshoot?", "A": "Our primary cameras record to dual card slots in real-time, providing immediate data redundancy. All files are immediately copied to encrypted local drives at the end of the session."},
        {"Q": f"What wardrobe styles do you recommend for {name.lower()}?", "A": "We recommend solid, cohesive tones (jewel colors, neutral shades, or soft pastels) and styling that avoids busy patterns. A wardrobe guide is shared prior to the shoot."},
        {"Q": f"Can we customize our {name.lower()} packages?", "A": "Absolutely. We offer tailored collections that adapt to your schedule, coverage needs, and specific print album requirements. Let us know your vision during your consultation."},
        {"Q": "What is your 100% Identity Promise?", "A": "We pledge never to alter your facial features, whiten skin artificially, or apply synthetic face-swapping filters. We capture you naturally and authentically, at your absolute best."},
        {"Q": "How far in advance should we book?", "A": "We recommend booking 3 to 6 months in advance for standard portraits, and 6 to 12 months in advance for peak wedding season commissions."},
        {"Q": "Do you offer custom print albums?", "A": "Yes, we source hand-crafted, fine-art albums printed on archival paper. We custom-design the layout for each couple or family to deliver a true heirloom product."},
        {"Q": "Is makeup and hair styling included?", "A": "Depending on the package, makeup and hair services can be bundled or coordinated with our partner stylists. We assist in styling recommendations for all sessions."}
    ]
    
    service_faqs = []
    for idx, t in enumerate(faq_topics):
        service_faqs.append({
            'question': t['Q'],
            'answer': t['A'],
            'order': idx + 1
        })
        
    raw_price = int(price.replace('₹', '').replace(',', ''))
    packages = [
        {
            'name': 'Editorial Collection',
            'price': price,
            'description': f'A sophisticated {name.lower()} package designed for premium digital delivery and basic print needs.',
            'features': [
                'Up to 4 hours of dedicated on-location session coverage',
                'Fully retouched high-resolution digital files',
                'Private online gallery with password-protected sharing access',
                'Pre-shoot wardrobe and conceptual concept briefing',
                'Dual-card real-time backup security'
            ]
        },
        {
            'name': 'Royal Heirloom Collection',
            'price': f"₹{raw_price * 1.6:,.0f}",
            'description': f'Our signature full-service {name.lower()} collection providing absolute coverage, comprehensive styling, and custom physical legacy albums.',
            'features': [
                'Full day session coverage (up to 8 hours)',
                'Complete high-resolution digital archive with print release',
                'Custom designed, hand-bound leather fine-art album (40 pages)',
                '16x24 premium canvas wall print ready for display',
                'Complimentary behind-the-scenes cinematic teaser video reel',
                'Dedicated primary and secondary camera operators'
            ]
        }
    ]
    
    json_item = {
        'name': name,
        'slug': slug,
        'icon': icon,
        'focus_keywords': kw,
        'seo_title': seo_title,
        'meta_description': meta_desc,
        'short_description': short_desc,
        'brand': brand,
        'price': price,
        'generic_content': generic_content,
        'features': s['features'],
        'process': s['process'],
        'faqs': service_faqs,
        'packages': packages,
        'amritsar': {
            'seo_title': amritsar_title,
            'meta_description': amritsar_meta,
            'content': amritsar_content,
            'cta': amritsar_cta,
            'faqs': service_faqs
        },
        'delhi': {
            'seo_title': delhi_title,
            'meta_description': delhi_meta,
            'content': delhi_content,
            'cta': delhi_cta,
            'faqs': service_faqs
        }
    }
    
    json_data.append(json_item)
    
    csv_rows.append([
        name, slug, icon, brand, price, kw, seo_title, meta_desc, short_desc, generic_content,
        "\n".join(s['features']), "\n".join(s['process']),
        amritsar_title, amritsar_meta, amritsar_content, amritsar_cta, json.dumps(service_faqs, ensure_ascii=False),
        delhi_title, delhi_meta, delhi_content, delhi_cta, json.dumps(service_faqs, ensure_ascii=False),
        json.dumps(packages, ensure_ascii=False)
    ])

# Write JSON Seed File
with open(os.path.join(seeds_dir, 'services-seed.json'), 'w', encoding='utf-8') as f:
    json.dump(json_data, f, indent=4, ensure_ascii=False)

# Write CSV Seed File
with open(os.path.join(seeds_dir, 'services-seed.csv'), 'w', encoding='utf-8', newline='') as f:
    writer = csv.writer(f)
    writer.writerow(csv_headers)
    writer.writerows(csv_rows)

print("✅ Seeds successfully created under jjw-core/assets/seeds/")
