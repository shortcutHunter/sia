angular.module('siaApp').directive('hasSub', hasSubFunction);

function hasSubFunction()
{
    return {
        restrict: 'C',
        link: link
    }

    function link(scope, element)
    {
        element.on('click', (event) => {
            event.preventDefault();
            updateNavbar(event);
        });

        function updateNavbar(event)
        {
            let submenu = event.currentTarget.querySelector('.submenu');
            if( submenu.classList.contains('active') ) submenu.style.display = "block";
            if( submenu.style.display == "none" ) submenu.classList.add('active');
            else submenu.classList.remove('active');
            slideToggle(submenu, 300);
        }
    }
}