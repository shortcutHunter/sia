(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaKrsController', MahasiswaKrsController);

  MahasiswaKrsController.$inject = ['$q', 'dataservice', 'logger', '$scope', '$stateParams'];
  /* @ngInject */
  function MahasiswaKrsController($q, dataservice, logger, scope, stateParams) {
    var vm = this;
    vm.title = 'KRS Mahasiswa';
    vm.data = [];

    activate();

    function activate() {
      getData();
    }

    function getData() {
      return dataservice.getUrl('/mahasiswa/krs').then(function(response) {
        vm.data = response.data;
        logger.info('Data loaded');
      });
    }

  }
})();
