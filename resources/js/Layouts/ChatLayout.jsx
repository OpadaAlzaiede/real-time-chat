import TextInput from "@/Components/TextInput";
import { usePage } from "@inertiajs/react";
import { useEffect, useState } from "react";
import { PencilSquareIcon } from "@heroicons/react/24/solid";
import ConversationItem from "@/Components/Conversations/ConversationItem";

const ChatLayout = ({ children }) => {
    const page = usePage();
    const conversations = page.props.conversations;
    const selectedConversation = page.props.selectedConversation;
    const [localConversations, setLocalConversations] = useState([]);
    const [sortedConversations, setSortedConversations] = useState([]);
    const [onlineUsers, setOnlineUsers] = useState({});

    const isUserOnline = (userId) => onlineUsers[userId];

    const onSearch = (event) => {
        const searchValue = event.target.value.toLowerCase();

        if(searchValue) {
            setLocalConversations(
                conversations.filter((conversation) => {
                    return conversation.name.toLowerCase().includes(searchValue);
                })
            );
        }else {
            setLocalConversations(conversations);
        }
    }

    useEffect(() => {
        setSortedConversations(
            localConversations.sort((conversationA, conversationB) => {
                if(conversationA.blocked_at && conversationB.blocked_at) {
                    return conversationA.blocked_at > conversationB.blocked_at ? 1 : -1;
                }else if(conversationA.blocked_at) {
                    return 1;
                }else if (conversationB.blocked_at) {
                    return -1;
                }

                if(conversationA.last_message_date && conversationB.last_message_date) {
                    return conversationB.last_message_date.localeCompare(conversationA.last_message_date);
                }else if(conversationA.last_message_date) {
                    return -1;
                }else if(conversationB.last_message_date) {
                    return 1;
                }else {
                    return 0;
                }
            })
        );
    }, [localConversations]);

    useEffect(() => {
        setLocalConversations(conversations);
    }, [conversations]);

    useEffect(() => {
        Echo.join('online')
            .here((users) => { // I join
                const onlineUsersObject = Object.fromEntries(
                    users.map((user) => [user.id, user])
                );

                setOnlineUsers((previousOnlineUsers) => {
                    return { ...previousOnlineUsers, ...onlineUsersObject };
                });
            })
            .joining((user) => { // Someone joins
                setOnlineUsers((previousOnlineUsers) => {
                    const updatedUsers = { ...previousOnlineUsers };
                    updatedUsers[user.id] = user;
                    return updatedUsers;
                });
            })
            .leaving((user) => { // Someone leaves
                setOnlineUsers((previousOnlineUsers) => {
                    const updatedUsers = { ...previousOnlineUsers };
                    delete updatedUsers[user.id];
                    return updatedUsers;
                });
            })
            .error((error) => {
                console.log('error', error);
            });

        return () => {
            Echo.leave('online');
        };
    }, []);

    return (
        <>
            <div className="flex-1 w-full flex overflow-hidden">
                <div
                    className={
                        `transition-all w-full sm:w-[220px]
                        md:w-[300px] bg-slate-800 flex flex-col overflow-hidden
                        ${selectedConversation ? "-ml-[100%] sm:ml-0" : ""}`
                    }
                >
                    <div className="flex items-center justify-between py-2 px-3 text-xl font-medium">
                        My Conversations
                        <div className="tooltip tooltip-left" data-tip="New Group">
                            <button className="text-gray-400 hover:text-gray-200">
                                <PencilSquareIcon className="w-4 h-4 inline-block ml-2" />
                            </button>
                        </div>
                    </div>
                    <div className="p-3">
                        <TextInput
                            onKeyUp={onSearch}
                            placeholder="Search for user or group"
                            className="w-full"
                        />
                    </div>
                    <div className="flex-1 overflow-auto">
                        {
                            sortedConversations && sortedConversations.map((conversation) => (
                                <ConversationItem
                                    key={`${
                                        conversation.is_group ? "group_" : "user_"
                                    }${conversation.id}`}
                                    conversation={conversation}
                                    online={!!isUserOnline(conversation.id)}
                                    selectedConversation={selectedConversation}
                                />
                            ))}
                    </div>
                </div>

                <div className="flex-1 flex flex-col overflow-hidden">
                    {children}
                </div>
            </div>
        </>
    )
}

export default ChatLayout;
