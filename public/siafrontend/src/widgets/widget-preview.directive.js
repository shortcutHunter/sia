(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetPreview', widgetPreview);

    widgetPreview.$inject = ['$compile', 'dataservice'];

    /* @ngInject */
    function widgetPreview(compile, dataservice) {
        var directive = {
            restrict: 'EA',
            link: link,
            scope: {
                widgetPreview: '='
            }
        };
        return directive;

        function link(scope, element, attrs)
        {
            var BASE64_MARKER = ';base64,';

            element.on('click', function(){
                let model_value = scope.widgetPreview;
                let file_preview = false;
                let pdfFile = false;

                if (!model_value) {
                    return;
                }

                if (model_value.filetype.includes('image')) {
                    scope.type = "image";
                    file_preview = `${model_value.filetype};base64, ${model_value.base64}`;
                }else if(model_value.filetype.includes('pdf')){
                    scope.type = "pdf";
                    file_preview = '';
                    pdfFile = dataservice.getPdf(model_value.base64);
                }else{

                }

                scope.preview_modal = file_preview;
                scope.fileName = model_value.filename;
                scope.filetype = model_value.filetype;
                scope.base64 = model_value.base64;
                
                if (pdfFile || file_preview) {
                    let preview_modal = '<modal-preview file="preview_modal" mimetype="filetype" base64="base64" name="fileName" type="type"></modal-preview>';
                    let el = compile(preview_modal)(scope);
                }
            });
        }
    }

})();