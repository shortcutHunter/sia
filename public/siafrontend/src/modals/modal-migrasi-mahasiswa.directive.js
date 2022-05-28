(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalMigrasiMahasiswa', modalMigrasiMahasiswa);

  modalMigrasiMahasiswa.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalMigrasiMahasiswa(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/migrasi_mahasiswa',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;
      scope.form = {};

      scope.migrasi = migrasi;

      activate();

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function migrasi() {
        let dataUpdate = {
          karyawan_baru: scope.form.karyawan_id,
          dosen_pa_lama: scope.data.id
        };

        dataservice.postDataUrl('/migrasi/dosen/pa', dataUpdate).then(function(){
          $(element).modal('hide');
          state.reload();
        });
      }

    }
  }
})();
