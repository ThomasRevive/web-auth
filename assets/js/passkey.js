var $ = jQuery.noConflict();

$(document).ready(async function () {
    if (!await checkForPasskeySupport()) {
        return;
    }

    passkeyForm();
});

async function checkForPasskeySupport() {
    // Availability of `window.PublicKeyCredential` means WebAuthn is usable.
    // `isUserVerifyingPlatformAuthenticatorAvailable` means the feature detection is usable.
    // `​​isConditionalMediationAvailable` means the feature detection is usable.
    if (window.PublicKeyCredential &&
        PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable &&
        PublicKeyCredential.isConditionalMediationAvailable) {
        // Check if user verifying platform authenticator is available.

        const checkSupport = await Promise.all([
            PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable(),
            PublicKeyCredential.isConditionalMediationAvailable(),
        ]);

        if (!checkSupport.every(r => r === true)) {
            $("#passkey_container").html("Passkeys not supported");
            return false;
        }

        return true;
    }
    else {
        $("#passkey_container").html("Passkeys not supported");
        return false;
    }
}

function passkeyForm() {
    $("#passkey_form").submit(function (event) {
        event.preventDefault();

        const formData = $(this).serializeArray();

        // $.ajax({
        //     url: "/ajax/get-user.php",
        //     method: "post",
        //     data: formData,
        //     dataType: "json",
        //     success: async function (response) {
        //         await passkeyDialog(response);
        //     }
        // });

        $.ajax({
            url: "/ajax/generate-passkey-creds.php",
            method: "post",
            data: formData,
            dataType: "json",
            success: async function (response) {
                await passkeyDialog(response);
                // console.log(response);
            },
            error: function (error) {
                console.error(error);
            }
        });
    });

    $("#passkey_verify_form").submit(function (event) {
        event.preventDefault();

        const formData = $(this).serializeArray();

        $.ajax({
            url: "/ajax/get-passkey-args.php",
            method: "post",
            data: formData,
            dataType: "json",
            success: async function (response) {
                await verifyPasskey(response);
                // console.log(response);
            },
            error: function (error) {
                console.error(error);
            }
        });
    });
}

/**
 * convert RFC 1342-like base64 strings to array buffer
 * @param {mixed} obj
 * @returns {undefined}
 */
function recursiveBase64StrToArrayBuffer(obj) {
    let prefix = '=?BINARY?B?';
    let suffix = '?=';
    if (typeof obj === 'object') {
        for (let key in obj) {
            if (typeof obj[key] === 'string') {
                let str = obj[key];
                if (str.substring(0, prefix.length) === prefix && str.substring(str.length - suffix.length) === suffix) {
                    str = str.substring(prefix.length, str.length - suffix.length);

                    let binary_string = window.atob(str);
                    let len = binary_string.length;
                    let bytes = new Uint8Array(len);
                    for (let i = 0; i < len; i++)        {
                        bytes[i] = binary_string.charCodeAt(i);
                    }
                    obj[key] = bytes.buffer;
                }
            } else {
                recursiveBase64StrToArrayBuffer(obj[key]);
            }
        }
    }
}

function arrayBufferToBase64(buffer) {
    let binary = '';
    let bytes = new Uint8Array(buffer);
    let len = bytes.byteLength;
    for (let i = 0; i < len; i++) {
        binary += String.fromCharCode( bytes[ i ] );
    }
    return window.btoa(binary);
}

async function passkeyDialog(credData) {
    recursiveBase64StrToArrayBuffer(credData);

    console.log(credData);

    // const publicKeyCredentialCreationOptions = {
    //     challenge: new ArrayBuffer(16),
    //     rp: {
    //         name: "Auth Test",
    //         id: "auth.dev.local",
    //     },
    //     user: {
    //         id: new ArrayBuffer(16),
    //         name: userData.username,
    //         displayName: userData.username,
    //     },
    //     pubKeyCredParams: [{ alg: -7, type: "public-key" }, { alg: -257, type: "public-key" }],
    //     excludeCredentials: [],
    //     // excludeCredentials: [{
    //     //     id: *****,
    //     //     type: 'public-key',
    //     //     transports: ['internal'],
    //     // }],
    //     authenticatorSelection: {
    //         authenticatorAttachment: "platform",
    //         requireResidentKey: true,
    //     }
    // };

    const cred = await navigator.credentials.create(credData);

    console.log(cred);

    // create object
    const authenticatorAttestationResponse = {
        transports: cred.response.getTransports  ? cred.response.getTransports() : null,
        clientDataJSON: cred.response.clientDataJSON  ? arrayBufferToBase64(cred.response.clientDataJSON) : null,
        attestationObject: cred.response.attestationObject ? arrayBufferToBase64(cred.response.attestationObject) : null
    };

    // check auth on server side
    // rep = await window.fetch('server.php?fn=processCreate' + getGetParams(), {
    //     method  : 'POST',
    //     body    : JSON.stringify(authenticatorAttestationResponse),
    //     cache   : 'no-cache'
    // });

    // const authenticatorAttestationServerResponse = await rep.json();


    $.ajax({
        url: "/ajax/process-passkey-creds.php",
        method: "POST",
        data: authenticatorAttestationResponse,
        dataType: "json",
        success: function (response) {
            console.log(response);
        },
        error: function (error) {
            console.error(error);
        }
    });

    // prompt server response
    // if (authenticatorAttestationServerResponse.success) {
    //     window.alert(authenticatorAttestationServerResponse.msg || 'registration success');

    // } else {
    //     console.error(authenticatorAttestationServerResponse.msg);

    //     // throw new Error(authenticatorAttestationServerResponse.msg);
    // }

}

async function verifyPasskey(data) {
    recursiveBase64StrToArrayBuffer(data);

    // check credentials with hardware
    const cred = await navigator.credentials.get(data);

    // create object for transmission to server
    const authenticatorAttestationResponse = {
        id: cred.rawId ? arrayBufferToBase64(cred.rawId) : null,
        clientDataJSON: cred.response.clientDataJSON  ? arrayBufferToBase64(cred.response.clientDataJSON) : null,
        authenticatorData: cred.response.authenticatorData ? arrayBufferToBase64(cred.response.authenticatorData) : null,
        signature: cred.response.signature ? arrayBufferToBase64(cred.response.signature) : null,
        userHandle: cred.response.userHandle ? arrayBufferToBase64(cred.response.userHandle) : null
    };

    console.log(authenticatorAttestationResponse);
}