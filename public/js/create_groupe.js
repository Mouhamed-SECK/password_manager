
const saveGoupe = async (url, data) => {
    try {
        const result = await axios.post(url, data);
      
    } catch (error) {
        if (error.response.status === 403) {
            window.location = '/login'
        }
    }
}


window.onload = () => {
    const url = window.location.href;
    const GroupForm = document.querySelector('[name="group"]');

    const designeAdminButton = document.querySelector('#saveAdmin');

    const paginationBUtton = document.querySelectorAll(".page-item");

    let addAmdinBtns;
    let groupId;

    paginationBUtton.forEach(btn => {
        btn.addEventListener('click', setupIdsListener)
      });


    function setupIdsListener () {

        addAmdinBtns=  document.querySelectorAll('.addAmdinBtn')
        addAmdinBtns.forEach(btn => {
            btn.addEventListener('click', function handleClick(event) {
                 groupId = parseInt(btn.getAttribute('data-id'))
        
            });
          });
    }

    setupIdsListener()

   
    GroupForm.addEventListener("submit", onSubmit);
    designeAdminButton.addEventListener("click", handleAdminDesagnation);



    async function handleAdminDesagnation(event) {
        event.preventDefault();

        const password = document.querySelector("#managePasswordInput").value;
        const email = document.querySelector("#adminEmail").value;


        console.log(email,password )


        try {
            console.log(groupId)
            let result = await axios.post(url + '/verify', {password});

    
            if (result.data.isCorrectPassword) {
            
                
                result = await  axios.post(url + '/getGroupKey', {groupId}) 
                groupKey = result.data.key;
                console.log("Encrypted group key with super admin password", groupKey)
    
                groupKey = decryptData(password, groupKey);

                console.log("decripted group key", groupKey)

                const tempKey = generateSceureKey();

                newEncryptedKey = encryptData(groupKey, tempKey)

                data = {
                    newEncryptedKey, 
                    tempKey,
                    email,
                    groupId

                }
                result = await  axios.post(url + '/assignGroupAdmin', data) 
                window.location.reload()  ;       

                
               
            }
                  
        } catch (error) {
            if (error.response.status === 403) {
                window.location = '/login'
            }
        }


    



    }


    async function onSubmit(event) {
        event.preventDefault();
     

        password = GroupForm.elements['group_privateKey'].value;
        title = GroupForm.elements['group_title'].value;

     
        try {
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
    }

   
}
