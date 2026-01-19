# ArtiSpark Website Strategy

## Design Philosophy
**Goal:** Create a classy, elegant website that reflects the artistic nature of ArtiSpark while maintaining professionalism and appeal to the Australian market.

## Typography & Font Strategy
**Inspiration:** Olive theme (https://demos-heartenmade.com/olive/)

### Font Stack:
1. **Headings:** Elegant serif font (e.g., Playfair Display, Cormorant Garamond, or similar)
   - Creates sophistication and artistic flair
   - Used for main headings and hero sections

2. **Body Text:** Clean, readable sans-serif (e.g., Inter, Poppins, or Open Sans)
   - Ensures excellent readability
   - Modern and approachable

3. **Accent/Decorative:** Script font for special elements (optional, to complement logo)
   - Used sparingly for quotes, taglines, or special callouts

### Typography Hierarchy:
- H1: Large, elegant serif (48-64px on desktop)
- H2: Medium serif (32-40px)
- H3: Smaller serif or bold sans-serif (24-28px)
- Body: 16-18px sans-serif with good line-height (1.6-1.8)

## Color Palette
**Based on Logo:**
- **Primary:** Teal-green (#7FB3A8, #A8C5C0) - from logo background
- **Accent:** Gold/Bronze (#D4AF37, #C9A961) - from logo border
- **Neutral:** Soft whites, warm grays (#F8F8F8, #2C2C2C)
- **Text:** Dark charcoal (#2C2C2C) for body, black for headings

## Layout & Structure

### Homepage Sections:
1. **Hero Section**
   - Large, elegant typography
   - Logo prominently displayed
   - Tagline: "Create Connect Craft"
   - Subtle background texture or gradient

2. **About/Introduction**
   - Brief, elegant description
   - Personal touch (founder story if applicable)

3. **Services/Classes** (Masonry Grid)
   - Children's Art Classes
   - Adult Workshops
   - Paint 'n Sip
   - Mosaic Workshops
   - Community Projects
   - Each card with elegant hover effects

4. **Gallery** (Masonry Grid - Primary Feature)
   - Showcase student work and studio atmosphere
   - Pinterest-style masonry layout
   - Lightbox on click

5. **Testimonials**
   - Elegant quote cards
   - Australian client testimonials

6. **Contact/Booking**
   - Clean contact form
   - Location (Australian address)
   - Social media links

## Masonry Grid Implementation

### Technology Options:
1. **CSS Grid with auto-fit** (Modern, lightweight)
2. **Masonry.js** (JavaScript library)
3. **Isotope.js** (More features, filtering)

### Recommendation: **CSS Grid + JavaScript for dynamic heights**
- Modern, performant
- Responsive by default
- Can add filtering/sorting later

### Grid Behavior:
- 3-4 columns on desktop
- 2 columns on tablet
- 1 column on mobile
- Smooth transitions and hover effects
- Images load lazily for performance

## Australian Market Considerations

1. **Content Tone:**
   - Warm, welcoming, community-focused
   - Professional yet approachable
   - Emphasize quality and craftsmanship

2. **Local Elements:**
   - Australian address format
   - Phone number format (04XX XXX XXX)
   - AEST timezone for booking
   - Australian English spelling

3. **Cultural Fit:**
   - Emphasize community involvement
   - Family-friendly messaging
   - Support for local artists

## Technical Stack

### Recommended:
- **HTML5** - Semantic structure
- **CSS3** - Modern styling, Grid, Flexbox
- **JavaScript (Vanilla or Lightweight)** - Interactivity, masonry behavior
- **Responsive Design** - Mobile-first approach

### Optional Enhancements:
- Lightweight CSS framework (Tailwind or custom)
- Image optimization (WebP format)
- Smooth scroll behavior
- Intersection Observer for animations

## Key Features

1. **Elegant Navigation**
   - Sticky header with logo
   - Smooth scroll to sections
   - Mobile hamburger menu

2. **Image Optimization**
   - Lazy loading
   - Responsive images
   - WebP with fallbacks

3. **Performance**
   - Minimal JavaScript
   - Optimized images
   - Fast loading times

4. **Accessibility**
   - Semantic HTML
   - ARIA labels
   - Keyboard navigation
   - Alt text for images

## Content Structure (Inspired by Art Studio 48)

### Pages/Sections:
1. Home
2. About
3. Services/Classes
4. Gallery
5. Contact/Booking
6. Shop (if applicable)

## Design Elements

1. **Spacing:** Generous whitespace for elegance
2. **Shadows:** Subtle, soft shadows for depth
3. **Borders:** Minimal, elegant (thin gold accents)
4. **Buttons:** Rounded corners, elegant hover states
5. **Images:** Rounded corners or full-bleed with elegant frames

## Next Steps

1. Create HTML structure
2. Implement CSS with typography and color scheme
3. Build masonry grid layout
4. Add responsive design
5. Implement smooth interactions
6. Optimize for performance
7. Test across devices
