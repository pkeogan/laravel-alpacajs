@pushonce('afterstyles:alpaca')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css" integrity="sha256-HtCCUh9Hkh//8U1OwcbD8epVEUdBvuI8wj1KtqMhNkI=" crossorigin="anonymous" />
<link type="text/css" href="//code.cloudcms.com/alpaca/1.5.24/bootstrap/alpaca.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.css" integrity="sha256-iu+Hq7JHYN0rAeT3Y+c4lEKIskeGgG/MpAyrj6W9iTI=" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" integrity="sha256-yMjaV542P+q1RnH6XByCPDfUFhmOafWbeLPmqKh11zo=" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" integrity="sha256-rDWX6XrmRttWyVBePhmrpHnnZ1EPmM6WQRQl6h0h7J8=" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rangeslider.js/2.3.2/rangeslider.css" integrity="sha256-jJApoDvazb6sRGbc3gE+wdEAE0cE0H1Ag3k1qCada9c=" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.22.1/css/jquery.fileupload.min.css" integrity="sha256-61g7gUn3l34rCtn25w4OWCrLuw8GYTmgwUDeXFAKxsU=" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.22.1/css/jquery.fileupload-ui.min.css" integrity="sha256-C71KlQGtMx1oiPRS8cs+cfP/e0eSkyI1ZJkNawvpxzs=" crossorigin="anonymous" />
<style>
.swal2-popup {
  font-size: 1.6rem !important;
}
.rangeslider__fill {
    background: #00a65a;
    position: absolute;
}
.rangeslider-display-data {
    background: #00a65a;
    position: absolute;
}
  .dialogWide > .modal-dialog {
     width: 80% !important;
 }
</style>
@endpushonce

@pushonce('scripts:alpaca')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js" integrity="sha256-CutOzxCRucUsn6C6TcEYsauvvYilEniTXldPa6/wu0k=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/ace.js" integrity="sha256-kCykSp9wgrszaIBZpbagWbvnsHKXo4noDEi6ra6Y43w=" crossorigin="anonymous"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.5/handlebars.min.js"></script>
<script type="text/javascript" src="//code.cloudcms.com/alpaca/1.5.24/bootstrap/alpaca.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/js/bootstrap-colorpicker.min.js" integrity="sha256-Y27a5HlqZwshkK8xfNfu6Y0c6+GGX9wTiRe8Xa8ITGY=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.26.12/sweetalert2.all.min.js" integrity="sha256-Y+WJP3jVjJgfw+/m2N5/GGUgxqXDCz7S3zstxj8pqng=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/es6-promise/4.1.1/es6-promise.auto.min.js" integrity="sha256-OI3N9zCKabDov2rZFzl8lJUXCcP7EmsGcGoP6DMXQCo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js" integrity="sha256-5YmaxAwMjIpMrVlK84Y/+NjCpKnFYa8bWWBbUHSBGfU=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js" integrity="sha256-eZNgBgutLI47rKzpfUji/dD9t6LRs2gI3YqXKdoDOmo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/rangeslider.js/2.3.2/rangeslider.min.js" integrity="sha256-vFhEtGnaQ2xB+yjBTSXxssthNcfdbzu+lmLYhCdp2Cc=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.js" integrity="sha256-awEOL+kdrMJU/HUksRrTVHc6GA25FvDEIJ4KaEsFfG4=" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.22.1/js/vendor/jquery.ui.widget.min.js" integrity="sha256-OvyxDbK1WMr6yVxduIqtCvDgG+n0zuxq5QiQOok7x5I=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.22.1/js/jquery.iframe-transport.min.js" integrity="sha256-64JcdNc4W47Ue2P1/xTqUx2OMXyQZXT4mM9jwstqy8Y=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.22.1/js/jquery.fileupload.min.js" integrity="sha256-YonB9QyI10SqVq8Gs2VRCYi0iI/h5+KoVHx/G1A3q48=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.22.1/js/jquery.fileupload-process.min.js" integrity="sha256-ePcoK5ltgAtj9CuyPsYFB+HOjKLOcpjJJLTANg1ZUCI=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.22.1/js/jquery.fileupload-ui.min.js" integrity="sha256-NP2W4G+PHtT0W8pJOuzVv0XJ4vtdFWBv0N60UvpSlDY=" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/ace.js" integrity="sha256-kCykSp9wgrszaIBZpbagWbvnsHKXo4noDEi6ra6Y43w=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/theme-twilight.js" integrity="sha256-tXxJnh+GgzPYX/6/GPBa/D3ru2+6nRORhjvQ21aqCHc=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/mode-javascript.js" integrity="sha256-OmCtgA83N4e+ItzF8fjdtdPuQe3LOeCZzDEZglAf4Ik=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/mode-json.js" integrity="sha256-5EVk5VxgaaxewTfhTYpOb+ZtkrGZBodhy1bhw+FcFoY=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/mode-php_laravel_blade.js" integrity="sha256-aPjQRDVlATWPqlpEJMMMv8JOHsb5mASFjHR4Z1nZ8Ss=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/mode-php.js" integrity="sha256-QipazUp3tNKur2jbEZoiqZAN2XtfMBgj4F1b8qiVecE=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/mode-html.js" integrity="sha256-tOmzi/fsSrNQAL0UX/fpQFNNgkMXlG04gM7wp/D3qhE=" crossorigin="anonymous"></script>

@endpushonce

@pushonce('afterstyles:select2')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.1/css/select2.min.css" integrity="sha256-MeSf8Rmg3b5qLFlijnpxk6l+IJkiR91//YGPCrCmogU=" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" integrity="sha256-nbyata2PJRjImhByQzik2ot6gSHSU4Cqdz5bNYL2zcU=" crossorigin="anonymous" />
<style>.select2-selection__rendered{margin-top: 0px !important;}</style>
@endpushonce


