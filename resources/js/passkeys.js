// Passkeys helper script
window.Passkeys = window.Passkeys || {
    isSupported() {
        return !!(window.PublicKeyCredential &&
            navigator.credentials &&
            navigator.credentials.create &&
            navigator.credentials.get);
    }
};
