(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalIsiTanggal', modalIsiTanggal);

  modalIsiTanggal.$inject = ['$state', 'dataservice', 'moment', 'logger'];

  /* @ngInject */
  function modalIsiTanggal(state, dataservice, moment, logger) {
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
      scope.status_pmb = attr.status;

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

        let tanggal_start = moment(scope.vm.data.tanggal, 'DD/MM/YYYY').toDate();
        let tanggal_end = moment(scope.vm.data.tanggal_end, 'DD/MM/YYYY').toDate();

        if (tanggal_end < tanggal_start) {
          logger.error("Tanggal mulai tidak dapat lebih besar dari tanggal akhir");
        } else {
          switch (data.status) {
            case 'terverifikasi':
              data['test_tertulis'] = scope.vm.data.tanggal;
              data['test_tertulis_end'] = scope.vm.data.tanggal_end;
              break;
            case 'test_lulus':
              data['test_kesehatan'] = scope.vm.data.tanggal;
              data['test_kesehatan_end'] = scope.vm.data.tanggal_end;
              break;
            case 'kesehatan_lulus':
              data['test_wawancara'] = scope.vm.data.tanggal;
              data['test_wawancara_end'] = scope.vm.data.tanggal_end;
              break;
            case 'wawancara_lulus':
              data['daftar_ulang'] = scope.vm.data.tanggal;
              data['daftar_ulang_end'] = scope.vm.data.tanggal_end;
              break;
          }

          dataservice.postData('pmb', data, scope.data.id).then(function(data){
            $(element).modal('hide');
            state.reload();
          });
        }
      }
    }
  }
})();
