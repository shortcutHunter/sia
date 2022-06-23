(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalBayarTagihan', modalBayarTagihan);

  modalBayarTagihan.$inject = ['$state', 'dataservice', 'logger'];

  /* @ngInject */
  function modalBayarTagihan(state, dataservice, logger) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/bayar_tagihan',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;
      activate();

      scope.upload = upload;
      scope.cicilanAkhir = false;

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
        });
      }

      let totalTagihan = 0;
      $.each(scope.data.transaksi, (i, v) => {
        if (v.status == 'verified') {
          totalTagihan += 1;
        }
      });

      if (totalTagihan >= 2) {
        scope.cicilanAkhir = true;
      }

      function upload() {

        if (scope.cicilanAkhir) {
          if (scope.vm.data.nominal != scope.data.sisa_hutang) {
            logger.error("Mohon hubungi bagian keuangan jika tidak dapat melunaskan sisa hutang.");
          }
        }

        if (!scope.data.register_ulang) {
          if (scope.vm.data.nominal != scope.data.sisa_hutang) {
            logger.error("Tagihan tidak dapat dicicil.");
          }
        }

        let data = {
          transaksi: {
            nominal: scope.vm.data.nominal,
            tagihan_bukti_bayar: [{'file': scope.vm.data.file}]
          }
        };

        dataservice.postData('tagihan', data, scope.data.id).then(function(data){
          $(element).modal('hide');
          state.reload();
        });
      }
    }
  }
})();
