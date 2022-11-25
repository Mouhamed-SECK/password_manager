window.onload = () => {
    const url = window.location.href;
    const PasswordForm = document.querySelector("#passwordForm");
    const AdminPasswordFormBtn = document.querySelector("#createPassord");


    const incorrectPassword = document.querySelector('#incorrectPassword');

    const createPassordBtn = document.querySelector('#createPassord');

    AdminPasswordFormBtn.addEventListener("click", (e) => {
      e.preventDefault();

      PasswordForm.submit();
    })

    

   
    PasswordForm.addEventListener("submit",                                                                                    appapp);



    async function onSubmit(event) {
        event.preventDefault();
     

        const title = PasswordForm.elements['title'].value;
        const url = PasswordForm.elements['url'].value;
        const login = PasswordForm.elements['login'].value;
        const email = PasswordForm.elements['email'].value;
        const password = PasswordForm.elements['password'].value;
        const description = PasswordForm.elements['description'].value;





     
    /*    try {
            const result = await axios.post(url + '/verify', {password});
         
            if (result.data.isCorrectPassword) {
                groupKey = encryptData(password, generateSceureKey());

            console.log(groupKey);

                data = {
                    groupKey,
                    title
                }

                console.log(data)
                await saveGoupe(url + '/save', data); 
                window.location.reload()  ;       
            }

          
          
        } catch (error) {
            if (error.response.status === 403) {
                window.location = '/login'
            }
        }
           */
    }

 

   
}
