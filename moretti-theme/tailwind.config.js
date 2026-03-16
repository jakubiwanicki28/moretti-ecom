/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './**/*.php',
    './src/**/*.js',
    './woocommerce/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        // Neutral minimalist palette inspired by CEIN
        'sand': {
          50: '#faf9f7',
          100: '#f5f3ef',
          200: '#ebe7df',
          300: '#ddd7cb',
          400: '#ccc3b3',
          500: '#b8ad9a',
          600: '#9d8f7a',
          700: '#7d6f5d',
          800: '#5f5449',
          900: '#4a423a',
        },
        'taupe': {
          50: '#f9f8f6',
          100: '#f0edea',
          200: '#ddd7d0',
          300: '#c4bcb2',
          400: '#a89d90',
          500: '#8f8275',
          600: '#766a5d',
          700: '#5f554b',
          800: '#4a423a',
          900: '#3a342e',
        },
        'cream': '#f5f3ef',
        'charcoal': '#2a2826',
      },
      fontFamily: {
        sans: ['system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Helvetica', 'Arial', 'sans-serif'],
        display: ['Georgia', 'Cambria', 'Times New Roman', 'serif'],
      },
      container: {
        center: true,
        padding: {
          DEFAULT: '1rem',
          sm: '1.5rem',
          md: '2rem',
          lg: '2.5rem',
          xl: '3rem',
          '2xl': '4rem',
        },
      },
      spacing: {
        '18': '4.5rem',
        '22': '5.5rem',
      },
      maxWidth: {
        '8xl': '88rem',
        '9xl': '96rem',
      },
    },
  },
  plugins: [],
}
