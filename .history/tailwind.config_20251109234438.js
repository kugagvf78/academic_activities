/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary:  "#1e40af",
        secondary:"#3b82f6",
        accent:   "#0ea5e9",
        dark:     "#0f172a",
      },
      fontFamily: {
        oswald: ["Oswald", "sans-serif"],
      },
    },
  },
  safelist: [
    "bg-gradient-to-r","bg-gradient-to-br",
    "from-primary","to-accent","via-secondary",
    "from-blue-600","via-blue-700","to-cyan-600",
  ],
  plugins: [],
};
