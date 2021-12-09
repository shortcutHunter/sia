(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaKrsController', MahasiswaKrsController);

  MahasiswaKrsController.$inject = ['$q', 'dataservice', 'logger', '$scope', '$stateParams', '$compile', '$element'];
  /* @ngInject */
  function MahasiswaKrsController($q, dataservice, logger, scope, stateParams, compile, element) {
    var vm = this;
    vm.title = 'KRS Mahasiswa';
    vm.data = [];

    vm.pengajuanKrs = pengajuanKrs;

    activate();

    function activate() {
      getData();
    }

    function getData() {
      return dataservice.getUrl('/mahasiswa/krs').then(function(response) {
        vm.data = response;
        logger.info('Data loaded');
      });
    }

    function pengajuanKrs() {
      let el = "<modal-pengajuan-krs></modal-pengajuan-krs>";
      el = compile(el)(scope);
      $(element).append(el);
    }

  }
})();
