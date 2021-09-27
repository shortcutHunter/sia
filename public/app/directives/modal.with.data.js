
angular.module('siaApp').directive('modalWithData', modalWithDataFunction);

modalWithDataFunction.$inject = ['appService'];

function modalWithDataFunction(appService)
{
    return {
        restrict: 'E',
        link: link,
        templateUrl: function(elem,attrs){
            return attrs.target;
        },
        replace: true,
        scope: true
    }

    function link(scope, element, attrs)
    {        
        scope.vm = {data: {}};
        scope.vm.uid = attrs.uid;
        scope.vm.additionalData = scope[attrs.uid];

        active();
        function active()
        {
            let table_id = attrs.autoload;
            let table_name = attrs.table;
            let url = `/${table_name}/get`;

            if (table_id) {
                url = `${url}/${table_id}`;
            }

            if (table_name == 'pengajuan_ks') {
                url = `/${table_name}/get?mahasiswa_id=${table_id}&status=proses`;
            }

            appService.httpCall(url).then((response) => {
                if (table_name == 'pengajuan_ks') {
                    scope.vm.data = response.data[0];
                }else{
                    scope.vm.data = response.data;
                }
            });

            $(element).modal('show');

            $(element).on('shown.bs.modal', () => {
                
            });

            $(element).on('hidden.bs.modal', () => {
                element.remove();
            });
        }
    }
}