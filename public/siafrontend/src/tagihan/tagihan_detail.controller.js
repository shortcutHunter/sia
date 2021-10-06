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

    scope.$watch('vm.data.upload_file', uploadFileChanges);

    activate();

    function activate() {
      var promises = [getDataDetail(), getOption()];
      return $q.all(promises).then(function() {
        logger.info('Data loaded');
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
          file: newVal,
          tagihan_id: vm.data.id
        };
        return dataservice.postData('tagihan_bukti_bayar', data).then(function(response) {
          state.reload();
        });
      }
    }

  }
})();
