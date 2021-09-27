angular.module('siaApp').directive('btnKonfigurasiNilai', btnKonfigurasiNilaiFunction);

btnKonfigurasiNilaiFunction.$inject = ['$route', 'appService'];

function btnKonfigurasiNilaiFunction(route, appService)
{
    return {
        restrict: 'C',
        link: link
    }

    function link(scope, element, attrs)
    {
        scope.$watch(() => scope, function(newVal){
            if (newVal.vm) {
                let scope_data = newVal.vm;
                if (scope_data.additionalData.konfigurasi_nilai && scope_data.additionalData.konfigurasi_nilai.length > 0) {
                    scope.vm.konfigurasi_nilai = {};
                    scope.vm.related_id = {};
                    scope.vm.is_update = true;
                    $.each(scope_data.additionalData.konfigurasi_nilai, function(i, v){
                        scope.vm.konfigurasi_nilai[v.nilai_id] = v.persentase;
                        scope.vm.related_id[v.nilai_id] = v.id;
                    });
                }
            }
        });

        element.on('click', () => {
            let data = scope.vm.data;
            let data_to_post = {
                multiple: true,
                data: []
            };

            if (scope.vm.is_update) {
                $.each(data, (i, v) => {
                    data_to_post['data'].push({
                        id: scope.vm.related_id[v.id],
                        mata_kuliah_diampuh_id: attrs.activeId,
                        nilai_id: v.id,
                        persentase: scope.vm.konfigurasi_nilai[v.id]
                    });
                });
                appService.httpCall(`/konfigurasi_nilai/update`, 'POST', data_to_post).then((response) => {
                    route.reload();
                });
            }else{
                $.each(data, (i, v) => {
                    data_to_post['data'].push({
                        mata_kuliah_diampuh_id: attrs.activeId,
                        nilai_id: v.id,
                        persentase: scope.vm.konfigurasi_nilai[v.id]
                    });
                });
                appService.httpCall(`/konfigurasi_nilai/add`, 'POST', data_to_post).then((response) => {
                    route.reload();
                });
            }
        });
    }
}