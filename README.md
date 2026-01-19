# ArtiSpark Website

An elegant, classy website for ArtiSpark - an art studio and creative workshop space in Australia.

## 🎨 Design Features

- **Elegant Typography**: Inspired by the Olive theme, using Playfair Display for headings and Inter for body text
- **Masonry Grid Layout**: Pinterest-style layout for gallery and services sections
- **Responsive Design**: Fully responsive, mobile-first approach
- **Australian Market**: Tailored for Australian audience with proper formatting and cultural considerations
- **Color Palette**: Based on your logo - teal-green primary colors with gold accents

## 📁 Project Structure

```
artiSpark/
├── index.html          # Main HTML structure
├── styles.css          # All styling and responsive design
├── script.js           # JavaScript for interactivity
├── logo_artispark.jpg  # Your logo file
├── STRATEGY.md         # Detailed design strategy
└── README.md           # This file
```

## 🚀 Getting Started

1. **Open the website**: Simply open `index.html` in your web browser
2. **Local Development**: For best results, use a local server:
   ```bash
   # Using Python 3
   python -m http.server 8000
   
   # Using Node.js (if you have http-server installed)
   npx http-server
   ```
   Then visit `http://localhost:8000`

## ✨ Key Features

### Typography
- **Headings**: Playfair Display (elegant serif)
- **Body**: Inter (clean, modern sans-serif)
- **Hierarchy**: Clear visual hierarchy with appropriate sizing

### Masonry Grid
- Automatic layout calculation based on content height
- Responsive: 3-4 columns (desktop), 2 columns (tablet), 1 column (mobile)
- Smooth transitions and hover effects

### Sections
1. **Hero**: Eye-catching introduction with tagline
2. **About**: Studio introduction and philosophy
3. **Services**: Masonry grid of service cards
4. **Gallery**: Masonry grid for artwork showcase
5. **Contact**: Contact form and information

### Interactive Elements
- Smooth scroll navigation
- Mobile hamburger menu
- Hover effects on cards and links
- Active navigation link highlighting
- Form validation

## 🎨 Customization

### Colors
Edit CSS variables in `styles.css`:
```css
:root {
    --primary-teal: #7FB3A8;
    --light-teal: #A8C5C0;
    --accent-gold: #D4AF37;
    /* ... */
}
```

### Content
- Update text content in `index.html`
- Replace placeholder gallery images with actual artwork
- Modify service descriptions as needed

### Adding Real Images
Replace gallery placeholders with actual images:
```html
<div class="gallery-card">
    <img src="path/to/image.jpg" alt="Description" class="gallery-image">
    <div class="gallery-overlay">
        <p class="gallery-caption">Your caption</p>
    </div>
</div>
```

## 📱 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## 🔧 Future Enhancements

- Add image lightbox for gallery
- Implement actual form submission backend
- Add booking system integration
- Include blog section
- Add shop/online store functionality
- Implement image lazy loading for better performance

## 📝 Notes

- The website uses modern CSS Grid for masonry layout
- All images should be optimized for web (WebP format recommended)
- Consider adding a favicon
- Update social media links in the contact section
- Add Google Analytics if needed

## 🎯 Australian Market Considerations

- Phone number format: 04XX XXX XXX
- Address format: Australian standard
- Timezone: AEST
- Australian English spelling throughout
- Community-focused messaging

## 📞 Support

For questions or customization needs, refer to the `STRATEGY.md` file for detailed design decisions and rationale.

---

**ArtiSpark** - Create • Connect • Craft
