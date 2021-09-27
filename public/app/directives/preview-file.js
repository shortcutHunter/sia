angular.module('siaApp').directive('previewFile', previewFileFunction);

previewFileFunction.$inject = ['$parse', 'appService'];

function previewFileFunction(parse, appService)
{
    return {
        restrict: "A",
        link: link,
        scope: {
            previewFile: '='
        }
    }

    function link(scope, element, attrs)
    {
        var BASE64_MARKER = ';base64,';
        
        element.on('click', function(){
            let model_value = scope.previewFile;
            let file_preview = false;
            if (model_value.filetype.includes('image')) {
                file_preview = `<img src="data:${model_value.filetype};base64, ${model_value.base64}" class="w-100">`;
            }else if(model_value.filetype.includes('pdf')){
                let rendered_pdf = appService.renderPDF(model_value.base64);
                
                rendered_pdf.then((rendered) => {
                    let data = {
                        modalTitle: model_value.filename,
                        modalContentElement: rendered,
                        modalFooterButton: 'button-close',
                        'size': "modal-lg"
                    };
                    appService.showModal(scope, data);
                });

            }else{

            }

            if (file_preview) {
                let data = {
                    modalTitle: model_value.filename,
                    modalContent: file_preview,
                    modalFooterButton: 'button-close',
                    'size': "modal-lg"
                };
                appService.showModal(scope, data);
            }
        });
    }
}