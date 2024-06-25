const express = require("express");
const cors = require("cors");

const app = express();

const port = process.env.PORT || 9876;

app.use(cors());

const appStoreLink = "https://apps.apple.com/us/app/kizuner/id1524617131";
const playStoreLink = "https://play.google.com/store/apps/details?id=com.kizuner";

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
            <meta name="twitter:card" content="app" />
            <meta name="twitter:site" content="@kizuner" />
            <meta name="twitter:title" content="${data.title}" />
            <meta name="twitter:description" content="${data.description}" />
            <meta name="twitter:image" content="${data.image_url}" />
            <meta name="twitter:app:name:iphone" content="Kizuner">
            <meta name="twitter:app:id:iphone" content="1524617131">
            <meta name="twitter:app:url:iphone" content="kizunerapp://${data.kind}/${data.id}" />
            <meta name="twitter:app:id:ipad" content="1524617131">
            <meta name="twitter:app:url:ipad" content="kizunerapp://${data.kind}/${data.id}" />
            <meta name="twitter:app:name:googleplay" content="Kizuner">
            <meta name="twitter:app:id:googleplay" content="com.kizuner">
            <meta name="twitter:app:url:googleplay" content="kizunerapp://${data.kind}/${data.id}" />
            <title>${data.title}</title>
        </head>
        <body>
        <script>
            window.location.href = "${data.storeLink}";
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

    let storeLink = playStoreLink
    if (/android/i.test(userAgent)) {
        // res.redirect('https://play.google.com/store/apps/details?id=com.yourapp');
    } else if (/iphone|ipad|ipod/i.test(userAgent)) {
        storeLink = appStoreLink
        // res.redirect('https://apps.apple.com/us/app/yourapp/idYOUR_APP_ID');
    }

    return res.send(
        buildShareContent({
            kind: k,
            id: id,
            title,
            description,
            image_url,
            storeLink
        })
    );
});

app.listen(port, () => {
    console.log(`Started at ${port}`);
});
