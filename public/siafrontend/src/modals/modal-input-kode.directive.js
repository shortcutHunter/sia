(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalInputKode', modalInputKode);

  modalInputKode.$inject = ['$state', 'dataservice', 'logger', '$compile'];

  /* @ngInject */
  function modalInputKode(state, dataservice, logger, compile) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/input_kode',
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.vm;
      
      scope.data = dm.data;
      scope.lanjutkan = lanjutkan;
      scope.vm.data = {};

      activate();

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function pengajuanKrs() {
        let el = "<modal-pengajuan-krs></modal-pengajuan-krs>";
        el = compile(el)(scope);
      }

      function lanjutkan() {
        if (!scope.vm.data.kode_pembayaran) {
          $('[ng-model="vm.data.kode_pembayaran"]').addClass('invalid');
        }else{
          $('[ng-model="vm.data.kode_pembayaran"]').removeClass('invalid');

          let url = "/mahasiswa/cek/kode";
          let data = {'kode': scope.vm.data.kode_pembayaran};

          dataservice.postDataUrl(url, data).then((response) => {
            if (response.status == 'success') {
              $(element).modal('hide');
              scope.vm.data = scope.data;
              pengajuanKrs();
            }else{
              logger.error('Maaf, kode pembayaran yang anda masukan tidak valid');
            }
          });
        }
      }

    }
  }
})();
