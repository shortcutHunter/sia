
angular.module('siaApp').directive('modalOne2Many', modalOne2ManyFunction);

modalOne2ManyFunction.$inject = ['appService'];

function modalOne2ManyFunction(appService)
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

        scope.deleteTableData = deleteTableData;

        active();
        function active()
        {
            if (attrs.autoload == 'mata_kuliah') {
                let semester_id = scope.$parent.vm.data.semester_id;
                semester_id = semester_id ? semester_id : 1;
                appService.httpCall(`/mata_kuliah/get?semester_id=${semester_id}`).then((response) => {
                    scope.vm.data.pengajuan_ks_detail = response.data;
                });
            }

            $(element).modal('show');

            $(element).on('shown.bs.modal', () => {
                
            });

            $(element).on('hidden.bs.modal', () => {
                element.remove();
            });
        }

        function deleteTableData(currentIndex, tableArray)
        {
            tableArray.splice(currentIndex, 1);
        }
    }
}