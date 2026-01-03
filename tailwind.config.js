export default {
    content: [
        './assets/**/*.js',
        './templates/**/*.html.twig'
    ],
    theme: {
        extend: {}
    },
    plugins: [
        require('daisyui')
    ],
    daisyui: {
        themes: ['dark', 'light']
    }
}
