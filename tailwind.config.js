export default {
    content: [
        './assets/**/*.js',
        './assets/**/*.css',
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
