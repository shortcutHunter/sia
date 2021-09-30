(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalTambahMahasiswa', modalTambahMahasiswa);

  modalTambahMahasiswa.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalTambahMahasiswa(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/tambah_mahasiswa',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;
      activate();

      scope.addMahasiswa = addMahasiswa;

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function addMahasiswa() {
        let data = {
          dosen_pa_id: dm.data.id,
          mahasiswa_id: scope.vm.data.mahasiswa_id
        };

        dataservice.postData('mahasiswa_bimbingan', data).then(function(data){
          $(element).modal('hide');
          state.reload();
        });
      }
    }
  }
})();
