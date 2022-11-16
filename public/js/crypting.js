const encryptData = (password, secret) => {
    const salt = CryptoJS.lib.WordArray.random(16);
    const iv = CryptoJS.lib.WordArray.random(16);
    const key = CryptoJS.PBKDF2(password, salt, { keySize: 256/32, iterations: 10000, hasher: CryptoJS.algo.SHA256});

    const encrypted = CryptoJS.AES.encrypt(secret, key, {iv: iv}).ciphertext;
    
    const concatenned =  CryptoJS.lib.WordArray.create().concat(salt).concat(iv).concat(encrypted)
    
   /* console.log({
        iv: iv.toString(CryptoJS.enc.Base64),
        salt: salt.toString(CryptoJS.enc.Base64),
        encrypted: encrypted.toString(CryptoJS.enc.Base64),
        concatenned: concatenned.toString(CryptoJS.enc.Base64)
      }); */
      
    return concatenned.toString(CryptoJS.enc.Base64);

}
const saveGoupe = async (url, data) => {
    console.log(url)
    try {
        const result = await axios.post(url, data);
      
    } catch (error) {
        if (error.response.status === 403) {
            window.location = '/login'
        }
    }
}

const decryptData = (password , secret) => {

    const encrypted =  CryptoJS.enc.Base64.parse(secret);

    const salt_len = iv_len = 16;

    const salt = CryptoJS.lib.WordArray.create(
        encrypted.words.slice(0, salt_len / 4 )
      );
      const iv = CryptoJS.lib.WordArray.create(
        encrypted.words.slice(0 + salt_len / 4, (salt_len+iv_len) / 4 )
      );
      
      const key = CryptoJS.PBKDF2(
        password,
        salt,
        { keySize: 256/32, iterations: 10000, hasher: CryptoJS.algo.SHA256}
      );
      
      const decrypted = CryptoJS.AES.decrypt(
        {
          ciphertext: CryptoJS.lib.WordArray.create(
            encrypted.words.slice((salt_len + iv_len) / 4)
          )
        },
        key,
        {iv: iv}
      ); 
      return decrypted.toString(CryptoJS.enc.Utf8);

}

const generateSceureKey =()  => CryptoJS.lib.WordArray.random(32).toString(CryptoJS.enc.Base64);



window.onload = () => {
  
    const GroupForm = document.querySelector('[name="group"]');

    GroupForm.addEventListener("submit", onSubmit);


    async function onSubmit(event) {
        event.preventDefault();
        const url = window.location.href 

        password = GroupForm.elements['group_privateKey'].value;
        title = GroupForm.elements['group_title'].value;
     
        try {
            const result = await axios.post(url + '/verify', {password});
         
            if (result.data.isCorrectPassword) {
                groupKey = encryptData(password, generateSceureKey());

                data = {
                    groupKey,
                    title
                }

                await saveGoupe(url + '/save', data); 
                window.location.reload()  ;       
            }

          
          
        } catch (error) {
            if (error.response.status === 403) {
                window.location = '/login'
            }
        }
    }

   
}
