import ChatLayout from '@/Layouts/ChatLayout';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useEffect, useState, useRef } from 'react';
import { ChatBubbleLeftRightIcon } from '@heroicons/react/24/solid';
import ConversationHeader from '@/Components/Conversations/ConversationHeader';
import MessageItem from '@/Components/Messages/MessageItem';

function Home({ selectedConversation = null, messages = null }) {

    const [localMessages, setLocalMessages] = useState([]);
    const messagesCtrRef = useRef(null);

    useEffect(() => {
        setLocalMessages(messages ? messages.data.reverse() : [] );
    }, [messages]);

    return  <>
        {!messages && (
            <div className='flex flex-col gap-8 justify-center items-center text-center h-full opacity-35'>
                <div className='text-2xl md:text-4xl p-16 text-slate-2000'>
                    Please select conversation to see messages
                </div>
                <ChatBubbleLeftRightIcon className='w-32 h-32 inline-block' />
            </div>
        )}
        {
            messages && (
                <>
                    <ConversationHeader
                        selectedConversation={selectedConversation}
                    />
                    <div
                        ref={messagesCtrRef}
                        className='flex-1 overflow-y-auto p-5'
                    >
                        {localMessages.length === 0 && (
                            <div className='flex justify-center items-center h-full'>
                                <div className='text-lg text-slate-200'>
                                    No messages found
                                </div>
                            </div>
                        )}
                        {localMessages.length > 0 && (
                            <div className='flex-1 flex flex-col'>
                                {localMessages.map((message) => (
                                    <MessageItem
                                        key={message.id}
                                        message={message}
                                    />
                                ))}
                            </div>
                        )}
                    </div>
                    {/*<MessageInput conversation={selectedConversation} />*/}
                </>
            )
        }
    </>;
}

Home.layout = (page) => {
    return (
            <AuthenticatedLayout user={page.props.auth.user}>
                <ChatLayout children={page}/>
            </AuthenticatedLayout>
    )
}

export default Home;
