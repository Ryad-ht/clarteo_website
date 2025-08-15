# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Clartéo is a French business website for a data consulting company targeting SMEs. It's a static website with a dark premium theme that showcases data transformation services.

## Architecture

### Frontend Structure
- **Static HTML/CSS site** with responsive design using Flexbox/Grid
- **Dark theme** with CSS custom properties for consistent color palette
- **Multi-page structure**: index.html (home), solutions.html (services), contact.html (contact form), merci.html (thank you page)
- **Mobile-first responsive design** with specific mobile padding classes
- **Inter font** from Google Fonts for typography

### Key Components
- **Navigation system** with sticky header and mobile hamburger menu
- **Hero section** with grid layout and call-to-action buttons
- **Timeline component** ("L'Expérience Clartéo") on homepage
- **Team section** with co-founder profiles
- **Contact form** with PHP backend processing

### Backend
- **PHP contact form handler** (contact.php) with:
  - Honeypot spam protection
  - Email validation and sanitization
  - UTF-8 encoding support
  - IP logging for security

## File Structure

```
├── index.html          # Homepage with hero, timeline, team sections
├── solutions.html      # Services and pricing details  
├── contact.html        # Contact form
├── merci.html          # Thank you page after form submission
├── contact.php         # PHP form processor
├── style.css           # Global styles with CSS custom properties
└── images/             # SVG icons, avatars, screenshots
```

## Development Commands

This is a static website with no build process. For local development:

```bash
# Serve locally with PHP (for contact form testing)
php -S localhost:8000

# For static files only
python -m http.server 8000
```

## Styling Architecture

- **CSS Custom Properties** defined in `:root` for consistent theming
- **Responsive breakpoints** handled with mobile-first approach
- **Component-based styling** for cards, buttons, features
- **Grid layouts** for hero section, features, and content sections

## Contact Form Flow

1. HTML form in contact.html submits to contact.php
2. PHP validates input, checks honeypot, sanitizes data
3. Email sent to contact@clarteo.com
4. User redirected to merci.html

## Deployment

Designed for static hosting (GitHub Pages, Netlify) with PHP support for contact form functionality.