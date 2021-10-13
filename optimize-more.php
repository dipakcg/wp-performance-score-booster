<?php
function wppsb_optimize_more() {
?>
<style>
    .wppsb_referrals { background: #FFF; width: 250px; min-height: 340px; border: 1px solid #CCC; float: left; padding: 13px; position: relative; margin: 13px; color: #0073AA; font-size: 14px; line-height: 1.5; border-radius: 10px; }
    .read_more { font-weight: bold; color: #FF9900; }
    .wppsb_ref_title { margin: 0.5em 0 0.5em 0; }
    #wppsb_button { float: right; }
</style>

<div id="wppsb-optimize-more-content"> </div>

<script>
fetch('https://public.dipakgajjar.com/products.json')
    .then(response => {
        return response.json()
    })
    .then(sections => {
        let html = "";
        sections.forEach(section => {
            html += `<h3>${section.title}</h3>`;
            section.products.forEach(product => {
                html += `<div class="wppsb_referrals">
                <a href="${product.url}" target="_blank" ><img src="${product.picture}" alt="${product.title}" width="250" height="250" border="0"></a>
                <h3 class="wppsb_ref_title">${product.title}</h3>
                <p>${product.description}</p>
                <a href="${product.url}" target="_blank"> <button class="button button-primary" id="wppsb_button">${product.buttonText}</button> </a>
                </div>`
            });
            html += `<div class="clear"></div>`;
            html += `<div id='footer-thankyou'>${section.disclaimer}</div>`;
        })
        document.getElementById("wppsb-optimize-more-content").innerHTML = html;
    })
    .catch(err => {
        console.log(err)
        document.getElementById("wppsb-optimize-more-content").innerText = "\n\nOops! Something went wrong.\n\n";
    })
</script>

<?php
}
