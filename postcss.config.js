export default {
    plugins: {
        'tailwindcss/nesting': 'postcss-nesting',
        tailwindcss: {},
        autoprefixer: {},
        // Minify CSS in production
        ...(process.env.NODE_ENV === 'production' ? { cssnano: { preset: 'default' } } : {}),
    },
}
