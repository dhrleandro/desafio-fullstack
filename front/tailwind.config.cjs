/** @type {import('tailwindcss').Config} */
export default {
  content: ['./src/**/*.tsx'],
  theme: {
    extend: {
      colors: {
        primaryBackground: 'var(--primaryBackground)',
        secondaryBackground: 'var(--secondaryBackground)',
        primaryText: 'var(--primaryText)',
        secondaryText: 'var(--secondaryText)',
        accent: 'var(--accent)',
        accentText: 'var(--accentText)',
      },
    },
  },
  plugins: [],
}