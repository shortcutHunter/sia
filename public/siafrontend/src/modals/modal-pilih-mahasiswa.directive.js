(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalPilihMahasiswa', modalPilihMahasiswa);

  modalPilihMahasiswa.$inject = ['$state', 'dataservice', 'logger'];

  /* @ngInject */
  function modalPilihMahasiswa(state, dataservice, logger) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/pilih_mahasiswa',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;

      scope.buatTagihan = buatTagihan;
      scope.tambahMahasiswa = tambahMahasiswa;
      scope.data_mahasiswa = [];

      activate();

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function buatTagihan(action) {
        let data_mahasiswa = scope.data_mahasiswa.map((v) => v.id);
        let data = {
          'pembiayaan_tahun_ajar': scope.data.id,
          'mahasiswa': data_mahasiswa
        };
        dataservice.postDataUrl('/buat/tagihan/lainnya/pta', data).then(function(response){
          $(element).modal('hide');
          logger.success("Tagihan Berhasil Dibuat");
          state.reload();
        });
      }

      function tambahMahasiswa() {
        scope.data_mahasiswa.push(angular.copy(scope.selectedData['vm.data.mahasiswa_id']));
        element.find('.easy-autocomplete').find('input').val('');
      }
    }
  }
})();
