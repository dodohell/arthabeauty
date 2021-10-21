import { processImages } from './images'
//import proxy from '@fly/proxy'

const example = proxy("https://www.arthabeauty.com", {host: "www.arthabeauty.com"})
const images = processImages(example)

fly.http.respondWith(function (req) {
    const url = new URL(req.url)
    if (url.pathname.match(/\.(png|jpg|jpeg)$/)) {
        // this uses our processImages function to create webp versions of images 
        return images(req)
    } else {
        // this just goes to the origin, no extra processing  
        return example(req)
    }
}) 