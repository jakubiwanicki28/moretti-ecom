# Moretti E-commerce Theme - Setup Complete! 🎉

Custom WordPress + WooCommerce theme with Tailwind CSS inspired by CEIN design.

## ✅ What's Implemented

### Design Features
- **Minimalist aesthetic** with neutral color palette (beige, taupe, charcoal, cream)
- **Responsive grid system**: 4 columns desktop, 2 columns mobile
- **Product cards** with heart icon (wishlist), quick add (+) button, color swatches
- **Category filter pills** (Outerwear, Dresses, Skirts, etc.)
- **Clean typography** with proper spacing and hierarchy
- **Modern header** with logo, search, wishlist, cart counter
- **Multi-column footer** with newsletter signup and social icons
- **Homepage sections**: hero images, content blocks, image gallery

### Technical Features
- Custom WooCommerce templates
- AJAX quick add to cart functionality
- Responsive mobile menu
- Tailwind CSS with custom color system
- Product variations with color swatches
- Grid layouts that match CEIN design
- Sticky header with top banner
- Clean product sorting and filtering

## 🚀 Quick Start

### 1. Start Docker
```bash
docker-compose up -d
```

### 2. Install WordPress
Open: **http://localhost:8080**
- Follow WordPress installation wizard
- Create admin user

### 3. Install & Activate WooCommerce
In WordPress Admin:
1. **Plugins > Add New**
2. Search "WooCommerce"
3. **Install > Activate**
4. Complete WooCommerce setup wizard

### 4. Activate Theme
1. **Appearance > Themes**
2. Find "Moretti E-commerce Theme"
3. Click **Activate**

### 5. Compile Tailwind CSS

**Option A - Using Node container (Docker):**
```bash
docker exec -it moretti-node bash
npm install
npm run dev
```

**Option B - Locally (if you have Node.js):**
```bash
cd moretti-theme
npm install
npm run dev  # Watch mode - leave running
# OR
npm run build  # Production build (minified)
```

## 📱 View Your Site

- **Frontend**: http://localhost:8080
- **Admin**: http://localhost:8080/wp-admin
- **Shop Page**: http://localhost:8080/shop (after adding products)

## 🛍️ Adding Products

To see the design in action:

1. Go to **Products > Add New** in WordPress Admin
2. Add product title, description, price
3. Upload product images
4. Assign categories (Outerwear, Dresses, Skirts, etc.)
5. For color swatches: 
   - Add variations with "Color" attribute
   - Use color names: Black, White, Beige, Cream, Taupe, Gray, Brown
6. Click **Publish**

The products will automatically appear in the shop grid with the CEIN-style design!

## 🎨 Customization

### Colors
Edit `moretti-theme/tailwind.config.js`:
```js
colors: {
  'sand': { ... },
  'taupe': { ... },
  'cream': '#f5f3ef',
  'charcoal': '#2a2826',
}
```

### Top Banner
In WordPress: **Appearance > Customize > Top Banner**
- Enable/disable banner
- Edit banner text

### Navigation Menu
**Appearance > Menus**
- Create menu
- Assign to "Primary Menu" location

### Logo
**Appearance > Customize > Site Identity > Logo**
- Upload your logo
- Or leave blank for text logo (site name)

## 📂 File Structure

```
moretti-theme/
├── style.css                    # Theme header (required by WP)
├── functions.php                # Theme functions, hooks, AJAX
├── header.php                   # Header with nav, cart, search
├── footer.php                   # Footer with newsletter, social
├── index.php                    # Homepage template
├── woocommerce.php              # WooCommerce wrapper
│
├── woocommerce/                 # Custom WooCommerce templates
│   ├── archive-product.php      # Shop page with category pills
│   ├── content-product.php      # Product card component
│   └── loop/
│       ├── loop-start.php       # Grid wrapper start
│       └── loop-end.php         # Grid wrapper end
│
├── src/
│   └── css/
│       └── input.css            # Tailwind source with custom styles
│
├── assets/
│   ├── css/
│   │   └── main.css             # Compiled Tailwind (generated)
│   └── js/
│       └── main.js              # Custom JS, quick add to cart
│
├── package.json                 # Node dependencies
└── tailwind.config.js           # Tailwind configuration
```

## 🎯 Key Features Explained

### **Shop Page with Filters**
- **Search bar** - Live product search
- **Filter panel** - Collapsible with:
  - Price range (min/max)
  - Color filter (visual swatches)
  - Stock status (In Stock / On Backorder)
  - Clear filters button
- **Category pills** - Filter by Outerwear, Dresses, Skirts, etc.
- **Sort dropdown** - By price, popularity, date, rating

### Product Grid
- **Desktop**: 4 columns
- **Tablet**: 3 columns
- **Mobile**: 2 columns
- Auto-responsive with Tailwind

### Product Cards
- **Image**: Hover opacity effect
- **Heart icon**: Top-right (wishlist - placeholder)
- **Plus icon**: Bottom-right (quick add to cart with AJAX)
- **Color swatches**: Shows up to 4 colors with "+" indicator
- **Price**: Bold, prominent display

### Category Pills
- Rounded pill design
- Active state (filled) / Inactive state (outline)
- Auto-generated from WooCommerce categories
- Filters products by category

### **Single Product Page**
- **2-column layout** - Images on left, info on right
- **Product gallery** - Main image + thumbnails grid
- **Color variations** - Dropdown selector
- **Quantity selector** - +/- buttons
- **Add to cart button** - Large, prominent
- **Product details accordions:**
  - Description
  - Size & Fit
  - Care Instructions
  - Shipping & Returns
- **Product meta** - SKU, Categories, Tags
- **Related products** - 4-column grid below

### Quick Add to Cart
- Click (+) button on product card
- Adds 1 item to cart via AJAX
- Shows loading spinner
- Shows checkmark on success
- Updates cart counter in header

### Mobile Menu
- Hamburger icon appears on mobile
- Slide-down menu
- Touch-friendly

## 🔧 Development Commands

```bash
# Tailwind watch mode (development)
npm run dev

# Tailwind production build (minified)
npm run build

# Docker commands
docker-compose up -d          # Start containers
docker-compose down           # Stop containers
docker-compose restart        # Restart containers
docker-compose logs -f        # View logs

# Enter containers
docker exec -it moretti-wordpress bash
docker exec -it moretti-node bash
```

## 🌐 Database Access

- **Host**: localhost:3306
- **Database**: moretti_ecom
- **User**: moretti
- **Password**: moretti123
- **Root Password**: rootpassword

## 📝 Next Steps

1. ✅ Setup complete
2. ✅ Theme installed
3. ⏳ Add your products
4. ⏳ Upload product images
5. ⏳ Create product categories
6. ⏳ Customize colors/fonts if needed
7. ⏳ Test checkout flow
8. ⏳ Add real content to homepage
9. ⏳ Configure payment methods
10. ⏳ Deploy to production server

## 🚀 Deployment (Coming Later)

When ready to deploy:
1. Export WordPress database
2. Copy `wordpress/` folder contents
3. Copy `moretti-theme/` to production server
4. Update `wp-config.php` with production database credentials
5. Run `npm run build` for production CSS

## 🐛 Troubleshooting

**Theme doesn't appear in WordPress**
- Check Docker volume mount: `docker-compose restart`
- Verify theme files exist: `docker exec -it moretti-wordpress ls /var/www/html/wp-content/themes/moretti-theme`

**CSS not loading**
- Compile Tailwind: `npm run dev` or `npm run build`
- Check `moretti-theme/assets/css/main.css` exists
- Clear WordPress cache
- Hard refresh browser (Cmd+Shift+R)

**Products not showing in grid**
- Make sure you've added products in **Products > Add New**
- Publish products (not draft)
- Assign categories to products

**Cart count not updating**
- Check browser console for JavaScript errors
- Verify `morettiData` is defined (view page source)

## 💡 Tips

- **Lorem Ipsum content** can be edited in WordPress Pages or directly in `index.php`
- **Hero images**: Upload featured image to homepage in **Pages > Home > Featured Image**
- **Product images**: Use high-quality images (minimum 800x1000px) for best results
- **Categories**: Create these in **Products > Categories**: Outerwear, Dresses, Skirts, Pants & Leggings, Stretch, Lounge
- **Color attributes**: In **Products > Attributes** create "Color" attribute with values: Black, White, Beige, Cream, Taupe, Gray, Brown

## 🎨 Design Credits

Design inspired by CEIN (contemporary minimalist e-commerce aesthetic).

---

**Need help?** Check the WordPress Codex or WooCommerce documentation.

**Ready to add products and see your beautiful shop!** 🛍️✨
