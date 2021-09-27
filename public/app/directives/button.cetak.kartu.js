angular.module('siaApp').directive('btnCetakKartu', buttonSaveFunction);

buttonSaveFunction.$inject = ['appService', '$route'];

function buttonSaveFunction(appService, route)
{
    return {
        restrict: 'A',
        link: link,
        scope: {}
    }

    function link(scope, element, attrs)
    {
        element.on('click', () => {
            let url = `/report/kartu_peserta/${attrs.btnCetakKartu}`;

            appService.httpCall(url, 'GET').then((response) => {
                let rendered_pdf = appService.renderPDF(response.data.content);
                rendered_pdf.then((rendered) => {
                    let data = {
                        modalTitle: "Kartu Peserta",
                        modalContentElement: rendered,
                        modalFooterElement: `
                            <a target="_blank" class="btn btn-primary" href="data:application/pdf;base64, ${response.data.content}" download="Kartu Peserta.pdf">Download</a>
                            <button class="btn" data-bs-dismiss="modal">Close</button>
                        `,
                        'size': "modal-lg"
                    };
                    appService.showModal(scope, data);
                });
            });
        });
    }
}