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

    vm.inputKode = inputKode;

    activate();

    function activate() {
      getData();
    }

    function getData() {
      return dataservice.getUrl('/mahasiswa/krs').then(function(response) {
        vm.data = response;
      });
    }

    function inputKode() {
      let el = "<modal-input-kode></modal-input-kode>";
      el = compile(el)(scope);
      $(element).append(el);
    }

  }
})();
