/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                // The Espresso Theme
                primary: {
                    DEFAULT: '#800000',
                    light:   '#A52A2A',
                    dark:    '#5C0000',
                    50:      '#FFF0F0',
                    100:     '#FFD9D9',
                    200:     '#FFB3B3',
                    300:     '#FF8080',
                    400:     '#FF4040',
                    500:     '#CC0000',
                    600:     '#800000',
                    700:     '#5C0000',
                    800:     '#3D0000',
                    900:     '#1F0000',
                },
                cream: {
                    DEFAULT: '#F5E8C7',
                    light:   '#FFF8EE',
                    dark:    '#E8D5A8',
                },
                charcoal: {
                    DEFAULT: '#121212',
                    light:   '#2A2A2A',
                    muted:   '#6B6B6B',
                },
                gold: {
                    DEFAULT: '#D4AF37',
                    light:   '#F0D060',
                    dark:    '#A87D20',
                },
            },
            fontFamily: {
                sans:    ['Inter', 'system-ui', 'sans-serif'],
                display: ['Outfit', 'Inter', 'sans-serif'],
                mono:    ['JetBrains Mono', 'monospace'],
            },
            backdropBlur: {
                xs: '2px',
            },
            animation: {
                'fade-in':     'fadeIn 0.3s ease-in-out',
                'slide-up':    'slideUp 0.4s ease-out',
                'slide-down':  'slideDown 0.3s ease-out',
                'bounce-in':   'bounceIn 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97)',
                'pulse-slow':  'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'shimmer':     'shimmer 1.5s infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%':   { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%':   { transform: 'translateY(20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideDown: {
                    '0%':   { transform: 'translateY(-10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                bounceIn: {
                    '0%, 20%, 50%, 80%, 100%': { transform: 'translateY(0)' },
                    '40%': { transform: 'translateY(-20px)' },
                    '60%': { transform: 'translateY(-10px)' },
                },
                shimmer: {
                    '0%':   { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
            },
            boxShadow: {
                'espresso':    '0 4px 24px rgba(128, 0, 0, 0.15)',
                'espresso-lg': '0 8px 48px rgba(128, 0, 0, 0.25)',
                'glass':       '0 8px 32px rgba(0, 0, 0, 0.1)',
                'card':        '0 2px 12px rgba(0, 0, 0, 0.08)',
                'card-hover':  '0 8px 32px rgba(0, 0, 0, 0.15)',
            },
        },
    },
    plugins: [],
}
