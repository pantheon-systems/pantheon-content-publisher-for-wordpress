/** @type {import('tailwindcss').Config} */
// tailwind.config.js
const plugin = require('tailwindcss/plugin');
module.exports = {
	content: ["./admin/**/*.{php,html,js}"],
	theme: {
		fontSize: {
			'xs': ['1.2rem', '1.8rem'],
			'sm': ['1.4rem', '2.1rem'],
			'base': ['1.6rem', '2.4rem'],
			'lg': ['1.8rem', '2.88rem'],
			'19.2': ['1.92rem', '2.88rem'],
			'xl': ['2rem', '3rem'],
			'2xl': ['2.4rem', '3.6rem'],
			'3xl': ['2.4rem', '3.84rem'],
			'6xl': ['3.2rem','5.12rem'],
			'7xl': ['3.2rem', '4.8rem'],
			'8xl': ['4.8rem', '4.8rem'],
			'9xl': ['6.4rem', '9.6rem'],
			'1.2': ['1.2rem', '1.68rem'],
			'1.3':['1.3rem','1.95rem'],
			'1.4':['1.4rem','1.95rem'],
			'1.5':['1.5rem', '2.25rem'],
			'1.6':['1.6rem', '2.25rem'],
			'1.8':['1.8rem','2.7rem'],
			'2': ['2rem', '3.2rem'],
			'2.4':['2.4rem','2.926rem'],
			'2.8':['2.8rem','4.2rem'],
		},
		colors: {
			'primary':'#3017A1',
			'secondary':'#664bd6',
			'black':'#000000',
			'grey':'#6D6D78',
			'light-grey':'#eeeeee',
			'white':'#ffffff',
		},
		extend: {
			fontFamily: {
				poppins: ['Poppins'],
			},
			screens: {
				'2xl': '1440px', // Custom breakpoint for 1440px
				'3xl': '1600px', // Custom breakpoint for 1600px
				'4xl': '1920px', // Custom breakpoint for 1920px
			},
			container: {
				center:true,
				padding: '4rem',
				screens: {
					sm: '100%',
					md: '100%',
					lg: '1024px',
					xl: '1160px',
					'2xl': '1280px',
					'3xl': '1280px',
					'4xl': '1280px',
				},
			},
		},
	},
	plugins: [
		plugin(function ({ addBase }) {
			addBase({
				'html': { fontSize: '62.5%' }, // Sets base font size to 10px (62.5% of 16px)
				'body': {
					'@apply font-poppins': {fontSize: '62.5%'}, // Applies Poppins font family
				},
			});
		}),
	],
}

