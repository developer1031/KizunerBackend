const express = require("express");
const cors = require("cors");

const app = express();

const port = process.env.PORT || 9876;

app.use(cors());

const buildShareContent = function (data) {
    const imageUrl = data.image_url.replace("https", "http");

    return `
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <meta property="fb:app_id" content="1239520603104986" />
            <meta property="al:android:package" content="com.kizuner" />
            <meta property="al:android:app_name" content="Kizuner" />
            <meta property="al:android:url" content="kizunerapp://${data.kind}/${data.id}" />
            <meta property="al:ios:url" content="kizunerapp://${data.kind}/${data.id}" />
            <meta property="al:ios:app_store_id" content="1524617131" />
            <meta property="al:ios:app_name" content="Kizuner" />
            <meta property="al:web:should_fallback" content="true" />
            <meta property="al:web:url" content="https://kizuner.com" />
            <meta property="og:title" content="${data.title}" />
            <meta property="og:description" content="${data.description}" />
            <meta property="og:type" content="website" />
            <meta property="og:url" content="https://kizuner.com" />
            <meta property="og:image" content="${imageUrl}" />
            <meta property="og:image:url" content="${imageUrl}" />
            <meta property="og:image:secure_url" content="${data.image_url}" />
            <meta property="og:image:width" content="1200" />
            <meta property="og:image:height" content="630" />
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:creator" content="@kizuner" />
            <meta name="twitter:title" content="${data.title}" />
            <meta name="twitter:description" content="${data.description}" />
            <meta name="twitter:image" content="${data.image_url}" />
            <meta name="twitter:app:country" content="US">
            <meta name="twitter:app:name:iphone" content="Kizuner">
            <meta name="twitter:app:id:iphone" content="1524617131">
            <meta name="twitter:app:url:iphone" content="https://apps.apple.com/us/app/id1524617131">
            <meta name="twitter:app:name:googleplay" content="Kizuner">
            <meta name="twitter:app:id:googleplay" content="com.kizuner">
            <meta name="twitter:app:url:googleplay" content="https://play.google.com/store/apps/details?id=com.kizuner">
            <title>${data.title}</title>
        </head>
        <body>
        <script>
        </script>
        </body>
        </html>
    `;
};

app.get("/k", (req, res) => {
    const { t, d, i, k, id } = req.query;

    const title = decodeURIComponent(t) || "Do What You Love - Kizuner";
    const description = decodeURIComponent(d);
    const image_id = decodeURIComponent(i);
    const image_url =
        image_id && image_id !== "undefined"
            ? `https://storage.googleapis.com/kizuner-storage-live/${image_id}`
            : "https://kizuner.com/wp-content/uploads/2020/07/Untitled-1.jpg";

    return res.send(
        buildShareContent({
            kind: k,
            id: id,
            title,
            description,
            image_url
        })
    );
});

app.listen(port, () => {
    console.log(`Started at ${port}`);
});
