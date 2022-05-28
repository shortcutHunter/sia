(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalIsiTanggal', modalIsiTanggal);

  modalIsiTanggal.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalIsiTanggal(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/isi_tanggal',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;
      scope.form_data = {};

      scope.updateStatus = updateStatus;

      scope.text_tanggal = "";

      switch (attr.status) {
          case 'terverifikasi':
            scope.text_tanggal = "Test Tertulis";
            break;
          case 'test_lulus':
            scope.text_tanggal = "Test Kesehatan";
            break;
          case 'kesehatan_lulus':
            scope.text_tanggal = "Test Wawancara";
            break;
          case 'wawancara_lulus':
            scope.text_tanggal = "Daftar Ulang";
            break;
        }

      activate();

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function updateStatus(action) {
        let data = {status: attr.status};

        switch (data.status) {
          case 'terverifikasi':
            data['test_tertulis'] = scope.data.tanggal;
            break;
          case 'test_lulus':
            data['test_kesehatan'] = scope.data.tanggal;
            break;
          case 'kesehatan_lulus':
            data['test_wawancara'] = scope.data.tanggal;
            break;
          case 'wawancara_lulus':
            data['daftar_ulang'] = scope.data.tanggal;
            break;
        }

        dataservice.postData('pmb', data, scope.data.id).then(function(data){
          $(element).modal('hide');
          state.reload();
        });
      }
    }
  }
})();
