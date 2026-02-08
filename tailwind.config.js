/** @type {import('tailwindcss').Config} */
export default {
  content: ["./src/views/**/*.php", "./src/views/**/*.html", "./public/**/*.js"],
  theme: {
    extend: {
      colors: {
        "regular-gradient-blue-cl": "var(--vcn-regular-gradient-blue-cl)",
        "regular-from-blue-cl": "var(--vcn-regular-from-blue-cl)",
        "regular-via-blue-cl": "var(--vcn-regular-via-blue-cl)",
        "regular-to-blue-cl": "var(--vcn-regular-to-blue-cl)",
        "regular-blue-1": "var(--vcn-regular-blue-1)",
        "regular-blue-2": "var(--vcn-regular-blue-2)",
        "regular-blue-3": "var(--vcn-regular-blue-3)",
        "regular-blue-4": "var(--vcn-regular-blue-4)",
        "regular-blue-5": "var(--vcn-regular-blue-5)",
        "regular-blue-cl": "var(--vcn-regular-blue-cl)",
        "regular-blue-hover-cl": "var(--vcn-regular-blue-hover-cl)",
        "scrollbar-track-bgcl": "var(--vcn-scrollbar-track-bgcl)",
        "scrollbar-thumb-bgcl": "var(--vcn-scrollbar-thumb-bgcl)",
        "regular-zalo-cl": "var(--vcn-regular-zalo-cl)",
        "regular-facebook-cl": "var(--vcn-regular-facebook-cl)",
        "regular-neon-cl": "var(--vcn-regular-neon-cl)",
        "regular-acc-state-cl": "var(--vcn-regular-acc-state-cl)",
        "regular-acc-state-from-cl": "var(--vcn-regular-acc-state-from-cl)",
        "regular-acc-state-via-cl": "var(--vcn-regular-acc-state-via-cl)",
        "regular-acc-state-to-cl": "var(--vcn-regular-acc-state-to-cl)",
        "regular-acc-card-bgcl": "var(--vcn-regular-acc-card-bgcl)",
      },
    },
  },
  plugins: [],
}
