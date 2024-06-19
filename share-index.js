const express = require("express");
const cors = require("cors");

const app = express();

const port = process.env.PORT || 9876;

app.use(cors());

const LinkAndroid = "https://play.google.com/store/apps/details?id=com.kizuner"
const LinkIPhone = "https://apps.apple.com/us/app/kizuner/id1524617131"

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
            <meta property="al:android:url" content="${LinkAndroid}" />
            <meta property="al:ios:url" content="${LinkIPhone}" />
            <meta property="al:ios:app_store_id" content="1524617131" />
            <meta property="al:ios:app_name" content="Kizuner" />
            <meta property="al:web:should_fallback" content="true" />
            <meta property="al:web:url" content="${data.dynamicLink}" />
            <meta property="og:title" content="${data.title}" />
            <meta property="og:description" content="${data.description}" />
            <meta property="og:type" content="website" />
            <meta property="og:url" content="https://kizuner.com" />
            <meta property="og:image" content="${imageUrl}" />
            <meta property="og:image:url" content="${imageUrl}" />
            <meta property="og:image:secure_url" content="${data.image_url}" />
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:creator" content="@kizuner" />
            <meta name="twitter:title" content="${data.title}" />
            <meta name="twitter:description" content="${data.description}" />
            <meta name="twitter:image" content="${data.image_url}" />
            <title>${data.title}</title>
        </head>
        <body>
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
            dynamicLink: `https://kizuner.com/${k}/${id}`,
            title,
            description,
            image_url
        })
    );
});

// app.get("/", (req, res) => {
//     return res.send(
//         buildShareContent({
//             dynamicLink: "https://kizuner.com",
//             title: "Do What You Love - Kizuner",
//             description: "",
//             image_url:
//                 "https://kizuner.com/wp-content/uploads/2020/07/Untitled-1.jpg"
//         })
//     );
// });

/**
 * DEPRECATED
 */
// app.get("/dynamic-link", (req, res) => {
//     const { deeplink, title, description, image_url } = req.query;

//     return res.send(
//         buildShareContent({
//             dynamicLink: decodeURIComponent(deeplink),
//             title: title || "Do What You Love - Kizuner",
//             description: description || "",
//             image_url:
//                 image_url && image_url !== "undefined"
//                     ? image_url
//                     : "https://kizuner.com/wp-content/uploads/2020/07/Untitled-1.jpg"
//         })
//     );
// });

app.listen(port, () => {
    console.log(`Started at ${port}`);
});
