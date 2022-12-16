window.onload = () => {
    const url = window.location.origin;
    const UserForm = document.querySelector('#userForm');

    const incorrectPassword = document.querySelector('#incorrectPassword');



    const email = document.querySelector('#email');
    const firstname = document.querySelector('#firstname');
    const lastname = document.querySelector('#lastname');

    const group = document.getElementById('group');

    let assignedGroup;
    let encryptedKey;

    group.addEventListener('change', function handleChange(event) {
         assignedGroup = event.target.value; 
          encryptedKey =  group.options[group.selectedIndex].getAttribute("data-privateKey")


   
    });

    UserForm.addEventListener("submit", onSubmit);

 
    async function onSubmit(event) {

    const password = document.querySelector('#userPassword').value;

        
        event.preventDefault();


         data = {
            email : email.value,
            firstname : firstname.value,
            lastname : lastname.value,
            group : assignedGroup,
            encryptedKey
        }

        console.log(data);
                          

     
        try {


            let result = await axios.post(url + '/admin/groups/verify', {password});


    
            if (result.data.isCorrectPassword) {
                console.log("it's okay");
                incorrectPassword.innerText = ""

                const groupKey = decryptData(password, data.encryptedKey);

                console.log("decripted group key", groupKey)

                const tempPassword = generateSceureKey(12);
                data.tempPassword = tempPassword;

                data.encryptedKey = encryptData(groupKey, tempPassword)

                console.log(data)


                result = await  axios.post(url + '/admin/users/saveAdmin', data) 

                if (result.data.success) {
                    window.location.reload() ;     

                } else {
                    incorrectPassword.innerHTML ="Cet utilisateur existe déjà";

                }     
            } else {
                incorrectPassword.innerText = "Votre mot de pass est incorrect"
            }
                  
        } catch (error) {
            if (error.response.status === 403) {
                window.location = '/login'
            }
        } 



    } 


   
}
