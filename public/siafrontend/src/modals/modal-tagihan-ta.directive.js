(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalTagihanTa', modalTagihanTa);

  modalTagihanTa.$inject = ['$state', 'dataservice', 'logger'];

  /* @ngInject */
  function modalTagihanTa(state, dataservice, logger) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/tagihan_ta',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;

      scope.buatTagihan = buatTagihan;

      activate();

      function activate() {
        $(element).modal('show');

        getMahasiswa();

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function getMahasiswa() {
        dataservice.getUrl(`/mahasiswa/get?semester_id=${scope.data.semester_id}&tahun_ajaran_id=${scope.data.tahun_ajaran_id}&status=mahasiswa`).then(function(response){
          scope.data_mahasiswa = response.data;
        });
      }

      function buatTagihan(action) {
        dataservice.postDataUrl('/buat/tagihan/pta', {'pembiayaan_tahun_ajar': scope.data.id}).then(function(response){
          $(element).modal('hide');
          logger.success("Tagihan Berhasil Dibuat");
          state.reload();
        });
      }
    }
  }
})();
