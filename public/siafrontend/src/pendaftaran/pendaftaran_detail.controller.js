(function() {
  'use strict';

  angular
    .module('app.pendaftaran')
    .controller('PendaftaranDetailController', PendaftaranDetailController);

  PendaftaranDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function PendaftaranDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Setup PMB';
    vm.table = 'pendaftaran';
    vm.data = {};

    vm.terbitkanNIM = terbitkanNIM;

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

    function terbitkanNIM() {
      return dataservice.postDataUrl('/terbitkan/nim', {'pendaftaran_id': vm.data.id}).then(function(response) {
        logger.success("NIM berhasil diterbitkan");
      });
    }
  }
})();
