<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentationTitle }}</title>
    <link rel="stylesheet" type="text/css" href="{{ l5_swagger_asset($documentation, 'swagger-ui.css') }}">
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-32x32.png') }}" sizes="32x32"/>
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-16x16.png') }}" sizes="16x16"/>
    <style>
    html
    {
        box-sizing: border-box;
        overflow: -moz-scrollbars-vertical;
        overflow-y: scroll;
    }
    *,
    *:before,
    *:after
    {
        box-sizing: inherit;
    }

    body {
      margin:0;
      background: #fafafa;
    }
    </style>
    @if(config('l5-swagger.defaults.ui.display.dark_mode'))
        <style>
            body#dark-mode,
            #dark-mode .scheme-container {
                background: #1b1b1b;
            }
            #dark-mode .scheme-container,
            #dark-mode .opblock .opblock-section-header{
                box-shadow: 0 1px 2px 0 rgba(255, 255, 255, 0.15);
            }
            #dark-mode .operation-filter-input,
            #dark-mode .dialog-ux .modal-ux,
            #dark-mode input[type=email],
            #dark-mode input[type=file],
            #dark-mode input[type=password],
            #dark-mode input[type=search],
            #dark-mode input[type=text],
            #dark-mode textarea{
                background: #343434;
                color: #e7e7e7;
            }
            #dark-mode .title,
            #dark-mode li,
            #dark-mode p,
            #dark-mode table,
            #dark-mode label,
            #dark-mode .opblock-tag,
            #dark-mode .opblock .opblock-summary-operation-id,
            #dark-mode .opblock .opblock-summary-path,
            #dark-mode .opblock .opblock-summary-path__deprecated,
            #dark-mode h1,
            #dark-mode h2,
            #dark-mode h3,
            #dark-mode h4,
            #dark-mode h5,
            #dark-mode .btn,
            #dark-mode .tab li,
            #dark-mode .parameter__name,
            #dark-mode .parameter__type,
            #dark-mode .prop-format,
            #dark-mode .loading-container .loading:after{
                color: #e7e7e7;
            }
            #dark-mode .opblock-description-wrapper p,
            #dark-mode .opblock-external-docs-wrapper p,
            #dark-mode .opblock-title_normal p,
            #dark-mode .response-col_status,
            #dark-mode table thead tr td,
            #dark-mode table thead tr th,
            #dark-mode .response-col_links,
            #dark-mode .swagger-ui{
                color: wheat;
            }
            #dark-mode .parameter__extension,
            #dark-mode .parameter__in,
            #dark-mode .model-title{
                color: #949494;
            }
            #dark-mode table thead tr td,
            #dark-mode table thead tr th{
                border-color: rgba(120,120,120,.2);
            }
            #dark-mode .opblock .opblock-section-header{
                background: transparent;
            }
            #dark-mode .opblock.opblock-post{
                background: rgba(73,204,144,.25);
            }
            #dark-mode .opblock.opblock-get{
                background: rgba(97,175,254,.25);
            }
            #dark-mode .opblock.opblock-put{
                background: rgba(252,161,48,.25);
            }
            #dark-mode .opblock.opblock-delete{
                background: rgba(249,62,62,.25);
            }
            #dark-mode .loading-container .loading:before{
                border-color: rgba(255,255,255,10%);
                border-top-color: rgba(255,255,255,.6);
            }
            #dark-mode svg:not(:root){
                fill: #e7e7e7;
            }
            #dark-mode .opblock-summary-description {
                color: #fafafa;
            }
        </style>
    @endif
</head>

<body @if(config('l5-swagger.defaults.ui.display.dark_mode')) id="dark-mode" @endif>
<div id="swagger-ui">
    <div style="padding: 20px; text-align: center;">
        <p>Loading Swagger UI...</p>
    </div>
</div>

<script>
    // Function to check if SwaggerUIBundle is loaded
    function checkSwaggerUILoaded() {
        if (typeof SwaggerUIBundle === 'undefined' || typeof SwaggerUIStandalonePreset === 'undefined') {
            return false;
        }
        return true;
    }

    // Function to initialize Swagger UI
    function initSwaggerUI() {
        if (!checkSwaggerUILoaded()) {
            document.getElementById('swagger-ui').innerHTML = '<div style="padding: 20px; text-align: center;"><h2>Error: Swagger UI libraries failed to load</h2><p>Please check that the asset files are accessible at:<br>{{ l5_swagger_asset($documentation, "swagger-ui-bundle.js") }}<br>{{ l5_swagger_asset($documentation, "swagger-ui-standalone-preset.js") }}</p></div>';
            return;
        }

        try {
            const urls = [];

            @foreach($urlsToDocs as $title => $url)
                urls.push({name: "{{ $title }}", url: "{{ $url }}"});
            @endforeach

            if (urls.length === 0) {
                document.getElementById('swagger-ui').innerHTML = '<div style="padding: 20px; text-align: center;"><h2>Error: No documentation URLs found</h2><p>Please check your l5-swagger configuration.</p></div>';
                return;
            }

            // Build a system
            const ui = SwaggerUIBundle({
                dom_id: '#swagger-ui',
                urls: urls,
                "urls.primaryName": "{{ $documentationTitle }}",
                operationsSorter: {!! isset($operationsSorter) ? '"' . $operationsSorter . '"' : 'null' !!},
                configUrl: {!! isset($configUrl) ? '"' . $configUrl . '"' : 'null' !!},
                validatorUrl: {!! isset($validatorUrl) ? '"' . $validatorUrl . '"' : 'null' !!},
                oauth2RedirectUrl: "{{ route('l5-swagger.'.$documentation.'.oauth2_callback', [], $useAbsolutePath) }}",

                requestInterceptor: function(request) {
                    request.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
                    return request;
                },

                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],

                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],

                layout: "StandaloneLayout",
                docExpansion : "{!! config('l5-swagger.defaults.ui.display.doc_expansion', 'none') !!}",
                deepLinking: true,
                filter: {!! config('l5-swagger.defaults.ui.display.filter') ? 'true' : 'false' !!},
                persistAuthorization: "{!! config('l5-swagger.defaults.ui.authorization.persist_authorization') ? 'true' : 'false' !!}",

                onComplete: function() {
                    console.log("Swagger UI loaded successfully");
                },

                onFailure: function(data) {
                    console.error("Swagger UI failed to load:", data);
                    document.getElementById('swagger-ui').innerHTML = '<div style="padding: 20px; text-align: center;"><h2>Error loading API documentation</h2><p>Please check the browser console for details.</p></div>';
                }
            })

            window.ui = ui

            @if(in_array('oauth2', array_column(config('l5-swagger.defaults.securityDefinitions.securitySchemes'), 'type')))
            ui.initOAuth({
                usePkceWithAuthorizationCodeGrant: "{!! (bool)config('l5-swagger.defaults.ui.authorization.oauth2.use_pkce_with_authorization_code_grant') !!}"
            })
            @endif
        } catch (error) {
            console.error("Error initializing Swagger UI:", error);
            document.getElementById('swagger-ui').innerHTML = '<div style="padding: 20px; text-align: center;"><h2>Error initializing Swagger UI</h2><p>' + error.message + '</p></div>';
        }
    }

    // Load scripts and initialize
    const bundleScript = document.createElement('script');
    bundleScript.src = "{{ l5_swagger_asset($documentation, 'swagger-ui-bundle.js') }}";
    bundleScript.onerror = function() {
        document.getElementById('swagger-ui').innerHTML = '<div style="padding: 20px; text-align: center;"><h2>Error: Failed to load swagger-ui-bundle.js</h2><p>Please check that the file exists and is accessible.</p></div>';
    };
    bundleScript.onload = function() {
        const presetScript = document.createElement('script');
        presetScript.src = "{{ l5_swagger_asset($documentation, 'swagger-ui-standalone-preset.js') }}";
        presetScript.onerror = function() {
            document.getElementById('swagger-ui').innerHTML = '<div style="padding: 20px; text-align: center;"><h2>Error: Failed to load swagger-ui-standalone-preset.js</h2><p>Please check that the file exists and is accessible.</p></div>';
        };
        presetScript.onload = function() {
            // Wait a bit for scripts to fully initialize
            setTimeout(function() {
                if (checkSwaggerUILoaded()) {
                    initSwaggerUI();
                } else {
                    document.getElementById('swagger-ui').innerHTML = '<div style="padding: 20px; text-align: center;"><h2>Error: SwaggerUIBundle not available after loading scripts</h2><p>Please check the browser console for errors.</p></div>';
                }
            }, 100);
        };
        document.body.appendChild(presetScript);
    };
    document.body.appendChild(bundleScript);
</script>
</body>
</html>
