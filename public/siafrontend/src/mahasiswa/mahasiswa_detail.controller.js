(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaDetailController', MahasiswaDetailController);

  MahasiswaDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$element', '$scope'];
  /* @ngInject */
  function MahasiswaDetailController($q, dataservice, logger, stateParams, compile, element, scope) {
    var vm = this;
    vm.title = 'Detail Mahasiswa';
    vm.table = 'mahasiswa';
    vm.data = {};

    vm.regUlang = regUlang;
    vm.pengajuanKrs = pengajuanKrs;

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        logger.info('Data loaded');
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }

    function regUlang() {
      let el = "<modal-register-ulang></modal-register-ulang>";
      el = compile(el)(scope);
      // $(element).append(el);
    }

    function pengajuanKrs() {
      let el = "<modal-pengajuan-krs></modal-pengajuan-krs>";
      el = compile(el)(scope);
      $(element).append(el);
    }
  }
})();
