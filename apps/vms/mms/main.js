var data;
data = {
    member: { },
    memberList: [{ }],
    memberContact: { },
    memberContacts: [{ }],
    memberPeriod: { },
    memberPeriods: [{ }],
    account: { },
    accountList: [{ }],
    accountBooking: { },
    accountBookings: [{ }],
};

$(document).ready(function() {
    new Vue( {
        el: '#dataSection',
        data:	data,
        methods:	{
        }
    }) ;
    $('.ui.dropdown').dropdown();
}) ;

function toggleNav() {
    $('.ui.left.labeled.icon.sidebar')
        .sidebar('setting', 'transition', 'overlay').sidebar('toggle')
    ;
}

function toggleRightBar() {
    $('.ui.right.labeled.icon.sidebar')
        .sidebar('setting', 'transition', 'overlay').sidebar('toggle')
    ;
}

function	showSection( _section) {
    $('.section').addClass( 'inactive') ;
    $('#'+_section).removeClass( 'inactive') ;
    toggleNav() ;
}

function	showTab( _tab) {
    $('.item').removeClass( 'active') ;
    $('#tab'+_tab).addClass( 'active') ;
    $('.tab').removeClass( 'active') ;
    $('#data'+_tab).addClass( 'active') ;
}

function	openSelector() {
    $('.ui.modal')
        .modal('show')
    ;
}

function	openEditor( e, _object) {
//    alert( $(e.target).data( 'id')) ;
    $('.ui.modal')
        .modal('show')
    ;
    var id = parseInt( $(e.target).data( 'id')) ;
    setId( _object, id, function( _reply) { data.memberContact = JSON.parse( _reply); }) ;
}

function	saveEditor( e, _object) {
    $('.ui.modal')
        .modal('hide')
    ;
    var id = parseInt( $(e.target).data( 'id')) ;
    upd( 'MemberContact', id, function( _reply) { data.memberContact = JSON.parse( _reply); }) ;
    return false ;
}

function	abortEditor( e) {
//    alert( $(e.target).data( 'id')) ;
    $('.ui.modal')
        .modal('hide')
    ;
    return false ;
}
/**
 * Loads the "Member" specified by the MemberNo in data.member.MemberNo.
 * _step does not have tp be provided or should be passed as 0.
 * @param e
 * @param _action
 * @param _step
 */
function    getMember( _action) {
    get( 'Member', _action, -1, data.member.MemberNo, '', function( _reply) {
        data.member = JSON.parse( _reply);
        get( 'Member', 'getListAsJSON', -1, data.member.MemberNo, 'MemberContact', function( _reply) {data.memberContacts = JSON.parse( _reply); }) ;
        get( 'Member', 'getListAsJSON', -1, data.member.MemberNo, 'MemberPeriod', function( _reply) {data.memberPeriods = JSON.parse( _reply); }) ;
    }) ;
}
function    getAccount( _action) {
    get( 'Account', _action, -1, data.account.AccountNo, '', function( _reply) {
        data.account = JSON.parse( _reply);
        get( 'Account', 'getListAsJSON', -1, data.account.AccountNo, 'AccountBooking', function( _reply) {data.accountBookings = JSON.parse( _reply); }) ;
    }) ;
}
function	get( _object, _action, _id, _key, _val, _func) {
    var postData	=	'' ;
    var actURL		=	'action.php';
    postData    +=  '_action=' + _action + '&_object=' + _object + '&_id=-1' + '&_key=' + _key + '&_val=' + _val;
    console.log( postData) ;
    $.ajax({
        type: 'POST',
        url: actURL,
        data: postData,
        success: function(_reply) {
            console.log( _reply);
            _func( _reply) ;
        },
        error: function (data) {
            alert( _action + ': an error occurred ...');
        }
    });
}
/**
 * Loads the "Member" specified by the MemberNo in data.member.MemberNo.
 * _step does not have tp be provided or should be passed as 0.
 * @param e
 * @param _action
 * @param _step
 */
function	setId( _object, _id, _func) {
    var postData	=	'' ;
    var actURL		=	'action.php';
    postData    +=  '_action=' + 'setId' + '&_object=' + _object + '&_id=' + _id  ;
        $.ajax({
            type: 'POST',
            url: actURL,
            data: postData,
            success: function(_reply) {
                console.log( _reply);
                _func( _reply) ;
            },
            error: function (data) {
                alert('vt3get: an error occurred ...');
            }
        });
    }

function	upd( _object, _id, _func) {
    var postData	=	'' ;
    var actURL		=	'action.php';
    postData    +=  '_action=' + 'upd' + '&_object=' + _object + '&_id=' + _id  ;
    postData    +=  '&json=' + JSON.stringify( data.memberContact);
    $.ajax({
        type: 'POST',
        url: actURL,
        data: postData,
        success: function(_reply) {
            console.log( _reply);
            _func( _reply) ;
        },
        error: function (data) {
            alert('vt3get: an error occurred ...');
        }
    });
}

function	keyup( e) {
    if (e.keyCode === 13) {
        getMember( e, 'getThis') ;
    }
}

/**
 * Updates the "Member" in the database.
 * @param e
 * @returns {boolean}
 */
function	save( e) {
    var postData	=	'' ;
    var url = 'vt3get.php';
    var myJSON = JSON.stringify( data.member);
    console.log( myJSON);
    postData	+=	'_action=save' ;
    postData    +=  '&MemberNo=' + $('#headMember input').val()  ;
    postData	+=	'&json=' + myJSON ;
    $.ajax({
        type: 'POST',
        url: url,
        data: postData,
        success: function(_reply) {
            console.log( _reply);
            data.member = JSON.parse(_reply);
            setTimeout( getMember, 2000, null, 'contacts', 1) ;
        },
        error: function (data) {
            alert('vt3get: an error occurred ... fuck ...');
        }
    });
    return false ;
}

/**
 * Creates s new "Member" in the database.
 * @param e
 * @returns {boolean}
 */
function	saveAsNew( e) {
    data.member.MemberNo = '' ;
    var postData	=	'' ;
    var url = 'vt3get.php';
    var myJSON = JSON.stringify( data.member);
    console.log( myJSON);
    postData	+=	'_action=saveAsNew' ;
    postData	+=	'&json=' + myJSON ;
    $.ajax({
        type: 'POST',
        url: url,
        data: postData,
        success: function(_reply) {
            console.log( _reply);
            data.member = JSON.parse(_reply);
            setTimeout( getMember, 500, null, 'contacts', 1) ;
        },
        error: function (data) {
            alert('vt3get: an error occurred ...');
        }
    });
    return false ;
}

function select( obj) {
//    data.member.MemberNo = '2015001';
    showTab('MemberMain');
    getMember(e, 'getThis', 0);
}

