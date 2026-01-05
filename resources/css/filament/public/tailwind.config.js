import preset from "../../../../vendor/filament/filament/tailwind.config.preset";

export default {
    presets: [preset],
    content: [
        "./app/Filament/**/*.php",
        "./resources/views/filament/**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
    ],
    safelist: [
        // Background colors
        'bg-blue-600',
        'bg-green-600',
        'bg-red-600',
        'bg-yellow-600',
        'bg-gray-200',
        'bg-gray-700',
        
        // Text colors
        'text-blue-100',
        'text-green-100',
        'text-red-100',
        'text-yellow-100',
        
        // Utilities used in progress bar
        'w-full',
        'rounded-full',
        'text-xs',
        'font-medium',
        'text-center',
        'p-0.5',
        'leading-none',
        
        // Dark mode
        'dark:bg-gray-700'
    ],
    theme: {
        extend: {
            colors: {
                secondary: {
                    50: "rgba(var(--secondary-50), <alpha-value>)",
                    100: "rgba(var(--secondary-100), <alpha-value>)",
                    200: "rgba(var(--secondary-200), <alpha-value>)",
                    300: "rgba(var(--secondary-300), <alpha-value>)",
                    400: "rgba(var(--secondary-400), <alpha-value>)",
                    500: "rgba(var(--secondary-500), <alpha-value>)",
                    600: "rgba(var(--secondary-600), <alpha-value>)",
                    700: "rgba(var(--secondary-700), <alpha-value>)",
                    800: "rgba(var(--secondary-800), <alpha-value>)",
                    900: "rgba(var(--secondary-900), <alpha-value>)",
                },
            },
        },
    },
};
