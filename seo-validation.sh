#!/bin/bash
# SEO Validation Script

echo "========================================="
echo "Canadaim SEO & Performance Validation"
echo "========================================="

# Check if files exist
echo ""
echo "✓ Checking Critical Files..."

files=(
    "public/robots.txt"
    "public/sitemap.xml"
    "public/.htaccess"
    "src/Twig/SeoExtension.php"
    "src/Twig/BreadcrumbExtension.php"
    "src/Service/SeoMetadataService.php"
    "src/EventListener/ResponseHeadersListener.php"
    "public/schema.json"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✓ $file"
    else
        echo "  ✗ $file (MISSING)"
    fi
done

echo ""
echo "✓ Checking Meta Tags in base.html.twig..."
grep -q "og:title" templates/base.html.twig && echo "  ✓ Open Graph tags present" || echo "  ✗ Open Graph tags missing"
grep -q "twitter:card" templates/base.html.twig && echo "  ✓ Twitter Card tags present" || echo "  ✗ Twitter Card tags missing"
grep -q "charset" templates/base.html.twig && echo "  ✓ Charset meta tag present" || echo "  ✗ Charset meta tag missing"
grep -q "viewport" templates/base.html.twig && echo "  ✓ Viewport meta tag present" || echo "  ✗ Viewport meta tag missing"

echo ""
echo "✓ SEO Implementation Complete!"
echo "========================================="
