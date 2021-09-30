(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalPengajuanKrs', modalPengajuanKrs);

  modalPengajuanKrs.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalPengajuanKrs(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/pengajuan_krs',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;
      scope.pengajuan_ks = [];

      scope.deleteTableData = deleteTableData;
      scope.tambahMatkul = tambahMatkul;
      scope.ajukan = ajukan;

      activate();

      function activate() {
        $(element).modal('show');

        getData();

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function getData() {
        dataservice.getDataFilter('mata_kuliah', `semester_id=${dm.data.semester_id || 1}`).then(function(response){
          scope.pengajuan_ks = response.data || [];
        });
      }

      function deleteTableData(indx) {
        scope.pengajuan_ks.splice(indx, 1);
      }

      function tambahMatkul() {
        let selected_data = scope.selectedData['vm.data.mata_kuliah_id'];
        scope.pengajuan_ks.push(selected_data);
        scope.vm.data.mata_kuliah_id = false;
      }

      function ajukan() {
        let data = {
          mahasiswa_id: dm.data.id,
          tahun_ajaran_id: dm.data.tahun_ajaran_id,
          semester_id: dm.data.semester_id,
          pengajuan_ks_detail: []
        };

        $.each(scope.pengajuan_ks, function(i, v){
          data.pengajuan_ks_detail.push({
            mata_kuliah_id: v.id
          });
        });

        dataservice.postData('pengajuan_ks', data).then(function(){
          $(element).modal('hide');
          state.reload();
        });
      }

    }
  }
})();
