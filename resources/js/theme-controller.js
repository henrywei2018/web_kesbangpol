/**
 * Public Panel Theme Controller
 * Integrates with Laravel GeneralSettings to apply dynamic theming
 */

class PublicPanelTheme {
    constructor() {
        this.settings = null;
        this.root = document.documentElement;
        this.init();
    }

    /**
     * Initialize the theme system
     */
    async init() {
        await this.loadThemeSettings();
        this.applyTheme();
        this.setupEventListeners();
        this.handleSystemTheme();
    }

    /**
     * Load theme settings from Laravel backend
     * This would typically be passed from your Blade template or API
     */
    async loadThemeSettings() {
        try {
            // In a real Laravel app, you would get this from:
            // 1. Meta tags in your Blade template
            // 2. API endpoint
            // 3. Inline JavaScript from the controller
            
            // Example of how to get it from meta tags:
            const themeData = document.querySelector('meta[name="theme-settings"]');
            if (themeData) {
                this.settings = JSON.parse(themeData.getAttribute('content'));
            } else {
                // Fallback default theme (matching your current red theme)
                this.settings = {
                    primary: 'rgb(253, 29, 29)',
                    secondary: 'rgb(99, 102, 241)',
                    gray: 'rgb(71, 85, 105)',
                    success: 'rgb(34, 197, 94)',
                    danger: 'rgb(239, 68, 68)',
                    warning: 'rgb(251, 191, 36)',
                    info: 'rgb(59, 130, 246)'
                };
            }
        } catch (error) {
            console.warn('Failed to load theme settings, using defaults', error);
            this.loadDefaultTheme();
        }
    }

    /**
     * Load default theme colors
     */
    loadDefaultTheme() {
        this.settings = {
            primary: 'rgb(253, 29, 29)',
            secondary: 'rgb(99, 102, 241)', 
            gray: 'rgb(71, 85, 105)',
            success: 'rgb(34, 197, 94)',
            danger: 'rgb(239, 68, 68)',
            warning: 'rgb(251, 191, 36)',
            info: 'rgb(59, 130, 246)'
        };
    }

    /**
     * Convert RGB string to RGB values
     */
    rgbStringToValues(rgbString) {
        const match = rgbString.match(/rgb\((\d+),\s*(\d+),\s*(\d+)\)/);
        if (match) {
            return `${match[1]}, ${match[2]}, ${match[3]}`;
        }
        return rgbString; // Return as-is if already in correct format
    }

    /**
     * Apply theme colors to CSS custom properties
     */
    applyTheme() {
        if (!this.settings) return;

        // Apply theme colors
        Object.entries(this.settings).forEach(([key, value]) => {
            const rgbValues = this.rgbStringToValues(value);
            this.root.style.setProperty(`--theme-${key}`, rgbValues);
        });

        // Generate color variations for primary color
        this.generateColorVariations('primary');
        this.generateColorVariations('secondary');

        // Dispatch theme applied event
        this.dispatchThemeEvent('theme-applied', { settings: this.settings });
    }

    /**
     * Generate color variations (opacity levels) for a given color
     */
    generateColorVariations(colorName) {
        const baseColor = this.settings[colorName];
        if (!baseColor) return;

        const rgbValues = this.rgbStringToValues(baseColor);
        
        // Generate opacity variations
        const variations = {
            50: 0.05,
            100: 0.1,
            200: 0.2,
            300: 0.3,
            400: 0.4,
            500: 1,
            600: 0.8,
            700: 0.7,
            800: 0.6,
            900: 0.9
        };

        Object.entries(variations).forEach(([level, opacity]) => {
            this.root.style.setProperty(
                `--theme-${colorName}-${level}`, 
                `${rgbValues}, ${opacity}`
            );
        });
    }

    /**
     * Setup event listeners for theme changes
     */
    setupEventListeners() {
        // Listen for theme updates from Laravel
        window.addEventListener('theme-updated', (event) => {
            this.settings = event.detail.settings;
            this.applyTheme();
        });

        // Listen for dark mode toggle
        document.addEventListener('click', (event) => {
            if (event.target.matches('[data-theme-toggle]')) {
                this.toggleDarkMode();
            }
        });

        // Listen for system theme changes
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (this.getThemePreference() === 'system') {
                    this.applySystemTheme(e.matches);
                }
            });
        }
    }

    /**
     * Handle system theme preference
     */
    handleSystemTheme() {
        const preference = this.getThemePreference();
        
        if (preference === 'system') {
            const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            this.applySystemTheme(isDark);
        } else if (preference === 'dark') {
            this.enableDarkMode();
        } else {
            this.enableLightMode();
        }
    }

    /**
     * Apply system theme based on OS preference
     */
    applySystemTheme(isDark) {
        if (isDark) {
            this.enableDarkMode();
        } else {
            this.enableLightMode();
        }
    }

    /**
     * Toggle dark mode
     */
    toggleDarkMode() {
        const currentTheme = this.getThemePreference();
        const html = document.documentElement;
        
        if (html.classList.contains('dark')) {
            this.enableLightMode();
            this.setThemePreference('light');
        } else {
            this.enableDarkMode();
            this.setThemePreference('dark');
        }
        
        this.dispatchThemeEvent('theme-mode-changed', { 
            mode: html.classList.contains('dark') ? 'dark' : 'light' 
        });
    }

    /**
     * Enable dark mode
     */
    enableDarkMode() {
        document.documentElement.classList.add('dark');
        this.updateThemeColorMeta('dark');
    }

    /**
     * Enable light mode
     */
    enableLightMode() {
        document.documentElement.classList.remove('dark');
        this.updateThemeColorMeta('light');
    }

    /**
     * Get theme preference from localStorage
     */
    getThemePreference() {
        return localStorage.getItem('theme-preference') || 'system';
    }

    /**
     * Set theme preference in localStorage
     */
    setThemePreference(preference) {
        localStorage.setItem('theme-preference', preference);
    }

    /**
     * Update theme-color meta tag for mobile browsers
     */
    updateThemeColorMeta(mode) {
        let metaThemeColor = document.querySelector('meta[name="theme-color"]');
        
        if (!metaThemeColor) {
            metaThemeColor = document.createElement('meta');
            metaThemeColor.name = 'theme-color';
            document.head.appendChild(metaThemeColor);
        }
        
        const primaryColor = this.settings?.primary || 'rgb(253, 29, 29)';
        const darkColor = mode === 'dark' ? 'rgb(30, 41, 59)' : primaryColor;
        
        metaThemeColor.content = darkColor;
    }

    /**
     * Dispatch custom theme events
     */
    dispatchThemeEvent(eventName, detail) {
        const event = new CustomEvent(eventName, { detail });
        window.dispatchEvent(event);
    }

    /**
     * Update theme from external source (e.g., Laravel settings update)
     */
    updateTheme(newSettings) {
        this.settings = { ...this.settings, ...newSettings };
        this.applyTheme();
        
        // Save to localStorage for persistence
        localStorage.setItem('theme-settings', JSON.stringify(this.settings));
    }

    /**
     * Generate CSS for dynamic styling
     */
    generateDynamicCSS() {
        if (!this.settings) return '';

        return `
            :root {
                --theme-primary: ${this.rgbStringToValues(this.settings.primary)};
                --theme-secondary: ${this.rgbStringToValues(this.settings.secondary)};
                --theme-gray: ${this.rgbStringToValues(this.settings.gray)};
                --theme-success: ${this.rgbStringToValues(this.settings.success)};
                --theme-danger: ${this.rgbStringToValues(this.settings.danger)};
                --theme-warning: ${this.rgbStringToValues(this.settings.warning)};
                --theme-info: ${this.rgbStringToValues(this.settings.info)};
            }
        `;
    }

    /**
     * Inject dynamic styles into the page
     */
    injectDynamicStyles() {
        const styleId = 'dynamic-theme-styles';
        let existingStyle = document.getElementById(styleId);
        
        if (existingStyle) {
            existingStyle.remove();
        }
        
        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = this.generateDynamicCSS();
        document.head.appendChild(style);
    }

    /**
     * Get current theme settings
     */
    getThemeSettings() {
        return this.settings;
    }

    /**
     * Check if dark mode is active
     */
    isDarkMode() {
        return document.documentElement.classList.contains('dark');
    }

    /**
     * Animate theme transitions
     */
    animateThemeTransition() {
        document.documentElement.style.transition = 'background-color 0.3s ease, color 0.3s ease';
        
        setTimeout(() => {
            document.documentElement.style.transition = '';
        }, 300);
    }
}

/**
 * Theme Utilities - Helper functions for theme manipulation
 */
class ThemeUtils {
    /**
     * Convert hex color to RGB values
     */
    static hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    /**
     * Convert RGB object to CSS RGB string
     */
    static rgbToCssString(rgb) {
        return `${rgb.r}, ${rgb.g}, ${rgb.b}`;
    }

    /**
     * Generate color palette from base color
     */
    static generateColorPalette(baseColor) {
        const rgb = this.hexToRgb(baseColor);
        if (!rgb) return {};

        const palette = {};
        const steps = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900];
        
        steps.forEach(step => {
            const factor = step <= 500 ? (500 - step) / 500 : (step - 500) / 500;
            const lightness = step <= 500 ? 1 - factor * 0.9 : 1 - factor * 0.6;
            
            palette[step] = {
                r: Math.round(rgb.r * lightness),
                g: Math.round(rgb.g * lightness),
                b: Math.round(rgb.b * lightness)
            };
        });

        return palette;
    }

    /**
     * Calculate contrast ratio between two colors
     */
    static getContrastRatio(color1, color2) {
        const luminance1 = this.getLuminance(color1);
        const luminance2 = this.getLuminance(color2);
        
        const brightest = Math.max(luminance1, luminance2);
        const darkest = Math.min(luminance1, luminance2);
        
        return (brightest + 0.05) / (darkest + 0.05);
    }

    /**
     * Calculate relative luminance of a color
     */
    static getLuminance(rgb) {
        const [r, g, b] = [rgb.r, rgb.g, rgb.b].map(c => {
            c = c / 255;
            return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
        });
        
        return 0.2126 * r + 0.7152 * g + 0.0722 * b;
    }

    /**
     * Check if a color is light or dark
     */
    static isLight(rgb) {
        const luminance = this.getLuminance(rgb);
        return luminance > 0.5;
    }
}

/**
 * Laravel Integration Helper
 */
class LaravelThemeIntegration {
    /**
     * Get theme settings from Laravel meta tag
     */
    static getThemeFromMeta() {
        const metaTag = document.querySelector('meta[name="theme-settings"]');
        if (metaTag) {
            try {
                return JSON.parse(metaTag.getAttribute('content'));
            } catch (error) {
                console.warn('Failed to parse theme settings from meta tag', error);
            }
        }
        return null;
    }

    /**
     * Update theme settings via AJAX (for real-time admin updates)
     */
    static async updateThemeSettings(settings) {
        try {
            const response = await fetch('/api/theme/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(settings)
            });
            
            if (response.ok) {
                const updatedSettings = await response.json();
                window.publicPanelTheme?.updateTheme(updatedSettings);
                return updatedSettings;
            }
        } catch (error) {
            console.error('Failed to update theme settings', error);
        }
        return null;
    }

    /**
     * Generate PHP array format for theme settings
     */
    static generatePhpThemeArray(settings) {
        const phpArray = Object.entries(settings)
            .map(([key, value]) => `    '${key}' => '${value}'`)
            .join(",\n");
            
        return `[\n${phpArray}\n]`;
    }
}

// Initialize theme system when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.publicPanelTheme = new PublicPanelTheme();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { PublicPanelTheme, ThemeUtils, LaravelThemeIntegration };
}