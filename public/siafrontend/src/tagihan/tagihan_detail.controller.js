(function() {
  'use strict';

  angular
    .module('app.tagihan')
    .controller('TagihanDetailController', TagihanDetailController);

  TagihanDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$scope', '$state'];
  /* @ngInject */
  function TagihanDetailController($q, dataservice, logger, stateParams, scope, state) {
    var vm = this;
    vm.title = 'Detail Tagihan';
    vm.table = 'tagihan';
    vm.data = {};
    vm.option = {};

    vm.tambahPembiayaan = tambahPembiayaan;
    vm.hapusBukti = hapusBukti;

    scope.$watch('vm.data.upload_file', uploadFileChanges);

    activate();

    function activate() {
      var promises = [getDataDetail(), getOption()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }

    function tambahPembiayaan() {
      $('#uploadFile').click();
    }

    function uploadFileChanges(newVal, oldVal) {
      if (newVal) {
        let data = {
          tagihan_bukti_bayar: []
        };

        $.each(newVal, (i, v) => {
          data.tagihan_bukti_bayar.push({'file': v});
        });

        return dataservice.postData('tagihan', data, vm.data.id).then(function(response) {
          state.reload();
        });
      }
    }

    function hapusBukti(data) {
      dataservice.deleteRecord('tagihan_bukti_bayar', data.id).then(() => state.reload());
    }

  }
})();
