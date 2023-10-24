const express = require('express')
const cors = require('cors')

const app = express()

const port = process.env.PORT || 9876

app.use(cors())

app.get('/', (req, res) => {
	
	console.log('called');
	
  return res.send(buildShareContent({
    deeplink: 'https://kizuner.page.link/vwrq',
    title: 'Do What You Love - Kizuner',
    description: '',
    image_url: 'https://kizuner.com/wp-content/uploads/2020/07/Untitled-1.jpg'
  }));
})

const buildShareContent = function(data) {
  return ('<!DOCTYPE html>' +
  '<html>' +
    '<head>' +
      '<meta property="fb:app_id" content="1239520603104986" />' +
      '<meta property="al:android:package" content="com.kizuner" />' +
      '<meta property="al:android:app_name" content="Kizuner" />' +
      '<meta property="al:android:url" content="'+ data.deeplink +'" />' +
      '<meta property="al:ios:url" content="'+ data.deeplink +'" />' +
      '<meta property="al:ios:app_store_id" content="1524617131" />' +
      '<meta property="al:ios:app_name" content="Kizuner" />' +
      '<meta property="al:web:should_fallback" content="true" />' +
      '<meta property="al:web:url" content="https://kizuner.com" />' + // dont know if this is required

      '<meta property="og:title" content="'+ data.title + '" />' +
      '<meta property="og:description" content="'+ data.description +'" />' +
      '<meta property="og:image" content="'+ data.image_url + '" />' +
      '<meta property="og:image:width" content="'+(data.width || 600)+'" />' + // this is for the preview image to load on first share
      '<meta property="og:image:height" content="'+(data.height || 315)+'" />' + // this is for the preview image to load on first share

      '<meta name="twitter:card" content="summary" />' +
      '<meta name="twitter:site" content="@kizuner" />' +
      '<meta name="twitter:creator" content="@kizuner" />' +
      '<meta name="twitter:title" content="'+ data.title +'" />' +
      '<meta name="twitter:description" content="'+ data.description +'" />' +
      '<meta name="twitter:image" content="' + data.image_url +'" />' +
    '</head>' +
    '<body>' +
      '<script>' +
        'window.location = "'+ data.deeplink +'"' +
      '</script>' +
    '</body>' +
  '</html>')
}

app.get('/dynamic-link', (req, res) => {
  const {deeplink, title, description, image_url} = req.query

  console.log(decodeURIComponent(deeplink), title)

  return res.send(buildShareContent({
    deeplink: decodeURIComponent(deeplink),
    title: title || 'Do What You Love - Kizuner',
    description: description || '',
    image_url: image_url && image_url !== 'undefined' ? image_url : 'https://kizuner.com/wp-content/uploads/2020/07/Untitled-1.jpg'
  }));
})

app.listen(port, () => {
  console.log(`Started at ${port}`)
})
