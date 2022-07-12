(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalMigrasiMatkul', modalMigrasiMatkul);

  modalMigrasiMatkul.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalMigrasiMatkul(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/migrasi_matkul',
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
      scope.removeMatkul = removeMatkul;

      activate();

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function removeMatkul($index) {
        scope.data.mata_kuliah_diampuh.splice($index, 1);
      }

      function migrasi() {
        let matkul_diampuh_ids = scope.data.mata_kuliah_diampuh.map(z => z.id);

        let dataUpdate = {
          karyawan_baru: scope.form.karyawan_id,
          dosen_pjmk_lama: scope.data.id,
          matkul_diampuh_ids: matkul_diampuh_ids
        };

        dataservice.postDataUrl('/migrasi/dosen/pjmk', dataUpdate).then(function(){
          $(element).modal('hide');
          state.reload();
        });
      }

    }
  }
})();
